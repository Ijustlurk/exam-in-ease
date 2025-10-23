<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login for mobile app
     * Supports both email and student ID number
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Can be email or ID number
            'password' => 'required|string',
            'device_name' => 'string|nullable',
        ]);

        // Try to find student by email or ID number
        $student = UserStudent::where('email_address', $request->login)
            ->orWhere('id_number', $request->login)
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Verify password
        if (!Hash::check($request->password, $student->password_hash)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if student is enrolled
        if ($student->status !== 'Enrolled') {
            return response()->json([
                'message' => 'Your account is not active. Please contact administration.',
            ], 403);
        }

        // Get or create base User if needed
        $user = User::find($student->user_id);
        if (!$user) {
            // Create base user if doesn't exist
            $user = User::create([
                'id' => $student->user_id,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email_address,
                'password' => $student->password_hash,
            ]);
        }

        // Create API token
        $deviceName = $request->device_name ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $student->user_id,
                'student_id' => $student->user_id,
                'id_number' => $student->id_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'middle_name' => $student->middle_name,
                'email' => $student->email_address,
                'status' => $student->status,
            ],
        ]);
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current user info
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $student = UserStudent::find($user->id);

        if (!$student) {
            return response()->json([
                'message' => 'Student profile not found',
            ], 404);
        }

        return response()->json([
            'user' => [
                'id' => $student->user_id,
                'student_id' => $student->user_id,
                'id_number' => $student->id_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'middle_name' => $student->middle_name,
                'email' => $student->email_address,
                'status' => $student->status,
            ],
        ]);
    }
}
