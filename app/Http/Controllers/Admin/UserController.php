<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAdmin;
use App\Models\UserProgramChair;
use App\Models\UserTeacher;
use App\Models\UserStudent;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Helper function to get the correct role-specific model
     */
    private function getRoleModel(string $role): ?string
    {
        return match ($role) {
            'student' => UserStudent::class,
            'instructor', 'teacher' => UserTeacher::class,  // Handle both 'instructor' and 'teacher'
            'programchair', 'chair' => UserProgramChair::class, // Handle both variations
            'admin' => UserAdmin::class,
            default => null,
        };
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        if (Gate::denies('admin-access')) {
            return view('errors.403');
        }

        // Gather all users from different tables
        $students = UserStudent::select('user_id', 'last_name', 'first_name', 'middle_name', 'email_address', 'created_at', 'status')
            ->addSelect(DB::raw("'student' as role"));

        $teachers = UserTeacher::select('user_id', 'last_name', 'first_name', 'middle_name', 'email_address', 'created_at', 'status')
            ->addSelect(DB::raw("'instructor' as role"));

        $chairs = UserProgramChair::select('user_id', 'last_name', 'first_name', 'middle_name', 'email_address', 'created_at', 'status')
            ->addSelect(DB::raw("'programchair' as role"));
        
        // Get admin users from main users table
        $adminUserIds = UserAdmin::pluck('user_id');
        $admins = User::whereIn('id', $adminUserIds)
            ->select('id as user_id', 'email as email_address', 'created_at')
            ->addSelect(DB::raw("'admin' as role"))
            ->addSelect(DB::raw("name as last_name"))
            ->addSelect(DB::raw("'' as first_name"))
            ->addSelect(DB::raw("'' as middle_name"))
            ->addSelect(DB::raw("'Active' as status"));

        // Combine all users
        $allUsers = collect();
        $allUsers = $allUsers->concat($students->get());
        $allUsers = $allUsers->concat($teachers->get());
        $allUsers = $allUsers->concat($chairs->get());
        $allUsers = $allUsers->concat($admins->get());
        
        // Sort by created_at
        $users = $allUsers->sortByDesc('created_at');

        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        if (Gate::denies('admin-access')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Base validation rules
        $rules = [
            'role' => 'required|string|in:student,instructor,programchair,admin',
            'password' => 'required|string|min:4',
        ];
        
        // Role-specific validation
        if ($request->role === 'admin') {
            $rules['username'] = 'required|string|max:100|unique:user_admin,username';
        } elseif ($request->role === 'student') {
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
            $rules['middle_name'] = 'nullable|string|max:100';
            $rules['email_address'] = 'required|email|max:150|unique:user_student,email_address';
            $rules['id_number'] = 'required|string|max:50|unique:user_student,id_number';
        } else { // Instructor or Chair
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
            $rules['middle_name'] = 'nullable|string|max:100';
            $rules['email_address'] = 'required|email|max:150|unique:user_teacher,email_address|unique:user_program_chair,email_address';
            $rules['username'] = 'required|string|max:100|unique:user_teacher,username|unique:user_program_chair,username';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Create main user record
            if ($request->role === 'admin') {
                $displayName = $request->username;
                $email = $request->username . '@admin.local'; // Placeholder email for admin
            } else {
                $displayName = $request->first_name . ' ' . $request->last_name;
                $email = $request->email_address;
            }
                                    
            $user = User::create([
                'name' => $displayName,
                'email' => $email,
                'password' => Hash::make($request->password), 
            ]);

            // Attach role
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
            
            // Create role-specific record
            $roleModel = $this->getRoleModel($request->role);
            
            if (!$roleModel) {
                throw new \Exception("Invalid user role specified.");
            }
            
            $data = ['user_id' => $user->id];
            
            if ($request->role === 'admin') {
                $data['username'] = $request->username;
                $data['password_hash'] = Hash::make($request->password);
            } elseif ($request->role === 'student') {
                $data['first_name'] = $request->first_name;
                $data['last_name'] = $request->last_name;
                $data['middle_name'] = $request->middle_name;
                $data['email_address'] = $request->email_address;
                $data['id_number'] = $request->id_number;
                $data['password_hash'] = Hash::make($request->password);
                $data['status'] = 'Enrolled';
            } else { // Teacher or Chair
                $data['first_name'] = $request->first_name;
                $data['last_name'] = $request->last_name;
                $data['middle_name'] = $request->middle_name;
                $data['email_address'] = $request->email_address;
                $data['username'] = $request->username;
                $data['password_hash'] = Hash::make($request->password);
                $data['status'] = 'Active';
            }
            
            $roleModel::create($data);

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User ' . $displayName . ' added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($user)) {
                $user->delete();
            }
            return redirect()->back()->with('error', 'Failed to add user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get user data for editing (AJAX)
     */
    public function edit($userId)
    {
        if (Gate::denies('admin-access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $mainUser = User::find($userId);
            if (!$mainUser) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            
            $role = $mainUser->roles()->first()?->name;
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'User role not defined'], 400);
            }

            // Get the role model class
            $roleModel = $this->getRoleModel($role);
            
            if (!$roleModel) {
                return response()->json(['success' => false, 'message' => 'Invalid role type: ' . $role], 400);
            }

            // Check if class exists
            if (!class_exists($roleModel)) {
                return response()->json(['success' => false, 'message' => 'Role model class not found: ' . $roleModel], 500);
            }

            $specificUser = $roleModel::where('user_id', $userId)->first(); 

            if (!$specificUser) {
                return response()->json(['success' => false, 'message' => 'Specific user details not found for role: ' . $role], 404);
            }
            
            $userData = $specificUser->toArray();
            $userData['role'] = $role;
            $userData['user_id'] = $mainUser->id;

            // Format data based on role
            if ($role === 'admin') {
                $userData['username'] = $userData['username'] ?? '';
                $userData['first_name'] = '';
                $userData['last_name'] = '';
                $userData['middle_name'] = '';
                $userData['email_address'] = '';
                $userData['id_number'] = '';
            } elseif ($role === 'student') {
                $userData['id_number'] = $userData['id_number'] ?? '';
            }

            return response()->json([
                'success' => true,
                'user' => $userData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error loading user data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update user
     */
    public function update(Request $request, $userId)
    {
        if (Gate::denies('admin-access')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $mainUser = User::findOrFail($userId);
        $oldRole = $mainUser->roles()->first()?->name;
        $newRole = $request->role;

        // Validation rules
        $rules = [
            'password' => 'nullable|string|min:4', 
            'role' => 'required|string|in:student,instructor,programchair,admin',
        ];
        
        // Role-specific validation
        if ($newRole === 'admin') {
            $rules['username'] = ['required', 'string', 'max:100', Rule::unique('user_admin', 'username')->ignore($userId, 'user_id')];
        } elseif ($newRole === 'student') {
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
            $rules['middle_name'] = 'nullable|string|max:100';
            $rules['email_address'] = ['required', 'email', 'max:150', Rule::unique('user_student', 'email_address')->ignore($userId, 'user_id')];
            $rules['id_number'] = ['required', 'string', 'max:50', Rule::unique('user_student', 'id_number')->ignore($userId, 'user_id')];
        } else { // Teacher or Chair
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
            $rules['middle_name'] = 'nullable|string|max:100';
            
            $tableToCheck = $newRole === 'instructor' ? 'user_teacher' : 'user_program_chair';
            $rules['email_address'] = ['required', 'email', 'max:150', Rule::unique($tableToCheck, 'email_address')->ignore($userId, 'user_id')];
            $rules['username'] = ['required', 'string', 'max:100', Rule::unique($tableToCheck, 'username')->ignore($userId, 'user_id')];
        }
        
        $request->validate($rules);
        
        DB::beginTransaction();
        try {
            // Update main users table
            if ($newRole === 'admin') {
                $displayName = $request->username;
                $email = $request->username . '@admin.local';
            } else {
                $displayName = $request->first_name . ' ' . $request->last_name;
                $email = $request->email_address;
            }
            
            $updateData = [
                'email' => $email,
                'name' => $displayName,
            ];
            
            if (!empty($request->password)) {
                $updateData['password'] = Hash::make($request->password);
            }
            $mainUser->update($updateData);

            // Handle role change
            $roleModel = $this->getRoleModel($newRole);
            $specificUser = $this->getRoleModel($oldRole)::where('user_id', $userId)->first();
            
            if ($oldRole !== $newRole) {
                // Delete old role record and detach
                if ($specificUser) {
                    $specificUser->delete();
                }
                $mainUser->roles()->detach();

                // Attach new role
                $role = Role::where('name', $newRole)->first();
                if ($role) {
                    $mainUser->roles()->attach($role->id);
                }
                $specificUser = null;
            }

            // Update or create role-specific record
            $specificUpdateData = [];

            if (!empty($request->password)) {
                $specificUpdateData['password_hash'] = Hash::make($request->password);
            }

            if ($newRole === 'admin') {
                $specificUpdateData['username'] = $request->username;
            } elseif ($newRole === 'student') {
                $specificUpdateData['first_name'] = $request->first_name;
                $specificUpdateData['last_name'] = $request->last_name;
                $specificUpdateData['middle_name'] = $request->middle_name;
                $specificUpdateData['email_address'] = $request->email_address;
                $specificUpdateData['id_number'] = $request->id_number;
            } else { // Teacher or Chair
                $specificUpdateData['first_name'] = $request->first_name;
                $specificUpdateData['last_name'] = $request->last_name;
                $specificUpdateData['middle_name'] = $request->middle_name;
                $specificUpdateData['email_address'] = $request->email_address;
                $specificUpdateData['username'] = $request->username;
            }

            // Update or Create
            if ($specificUser && count($specificUpdateData) > 0) {
                $specificUser->update($specificUpdateData);
            } else {
                $specificUpdateData['user_id'] = $userId;
                if (empty($specificUpdateData['password_hash'])) {
                    $specificUpdateData['password_hash'] = $specificUser->password_hash ?? Hash::make('password');
                }
                $roleModel::create($specificUpdateData);
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User ' . $displayName . ' updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        if (Gate::denies('admin-access')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $role = $user->roles()->first()?->name;

            if ($role) {
                $roleModel = $this->getRoleModel($role);
                if ($roleModel) {
                    $roleModel::where('user_id', $id)->delete();
                    $user->roles()->detach();
                }
            }

            $user->delete();

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template for importing users
     */
    public function downloadTemplate($role)
    {
        if (Gate::denies('admin-access')) {
            abort(403);
        }

        $headers = [];
        $sampleData = [];

        switch ($role) {
            case 'student':
                $headers = ['first_name', 'last_name', 'middle_name', 'email_address', 'id_number', 'password'];
                $sampleData = ['John', 'Doe', 'Smith', 'john.doe@example.com', 'STU2025-001', 'password123'];
                break;
            case 'instructor':
                $headers = ['first_name', 'last_name', 'middle_name', 'email_address', 'username', 'password'];
                $sampleData = ['Jane', 'Smith', 'Ann', 'jane.smith@example.com', 'jsmith', 'password123'];
                break;
            case 'programchair':
                $headers = ['first_name', 'last_name', 'middle_name', 'email_address', 'username', 'password'];
                $sampleData = ['Robert', 'Johnson', 'Lee', 'robert.johnson@example.com', 'rjohnson', 'password123'];
                break;
            default:
                abort(404);
        }

        $filename = $role . '_import_template.csv';
        
        $callback = function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $sampleData); // Include sample data
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import users from CSV/Excel file
     */
    public function import(Request $request)
    {
        if (Gate::denies('admin-access')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120', // 5MB max
            'role' => 'required|in:student,instructor,programchair,admin'
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $role = $request->role;
            
            // Read file based on extension
            $extension = $file->getClientOriginalExtension();
            $data = [];

            if ($extension === 'csv') {
                $data = $this->readCSV($file);
            } else {
                $data = $this->readExcel($file);
            }

            if (empty($data)) {
                return response()->json(['success' => false, 'message' => 'No data found in file'], 400);
            }

            // Get headers from first row
            $headers = array_shift($data);
            $headers = array_map('trim', $headers);

            // Validate headers based on role
            $requiredFields = $this->getRequiredFields($role);
            $missingFields = array_diff($requiredFields, $headers);
            
            if (!empty($missingFields)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Missing required columns: ' . implode(', ', $missingFields)
                ], 400);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because array is 0-indexed and we removed header row
                
                try {
                    // Map row data to headers
                    $userData = array_combine($headers, $row);
                    $userData = array_map('trim', $userData);
                    
                    // Skip empty rows
                    if (empty(array_filter($userData))) {
                        continue;
                    }

                    // Create user
                    $this->createUserFromImport($userData, $role);
                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Import completed: {$successCount} users imported successfully";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} failed. Errors: " . implode('; ', array_slice($errors, 0, 5));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $successCount,
                'failed' => $errorCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Read CSV file
     */
    private function readCSV($file)
    {
        $data = [];
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Read Excel file
     */
    private function readExcel($file)
    {
        // Using PhpSpreadsheet
        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            return $worksheet->toArray();
        } catch (\Exception $e) {
            throw new \Exception('Could not read Excel file. Please ensure it\'s a valid Excel file or use CSV format.');
        }
    }

    /**
     * Get required fields for role
     */
    private function getRequiredFields($role)
    {
        switch ($role) {
            case 'student':
                return ['first_name', 'last_name', 'email_address', 'id_number', 'password'];
            case 'instructor':
            case 'programchair':
                return ['first_name', 'last_name', 'email_address', 'username', 'password'];
            case 'admin':
                return ['username', 'password'];
            default:
                return [];
        }
    }

    /**
     * Create user from imported data
     */
    private function createUserFromImport($userData, $role)
    {
        // Create main user record
        if ($role === 'admin') {
            $displayName = $userData['username'];
            $email = $userData['username'] . '@admin.local';
        } else {
            $displayName = $userData['first_name'] . ' ' . $userData['last_name'];
            $email = $userData['email_address'];
        }

        $user = User::create([
            'name' => $displayName,
            'email' => $email,
            'password' => Hash::make($userData['password']),
        ]);

        // Attach role
        $roleRecord = Role::where('name', $role)->first();
        if ($roleRecord) {
            $user->roles()->attach($roleRecord->id);
        }

        // Create role-specific record
        $roleModel = $this->getRoleModel($role);
        $data = ['user_id' => $user->id];

        if ($role === 'admin') {
            $data['username'] = $userData['username'];
            $data['password_hash'] = Hash::make($userData['password']);
        } elseif ($role === 'student') {
            $data['first_name'] = $userData['first_name'];
            $data['last_name'] = $userData['last_name'];
            $data['middle_name'] = $userData['middle_name'] ?? '';
            $data['email_address'] = $userData['email_address'];
            $data['id_number'] = $userData['id_number'];
            $data['password_hash'] = Hash::make($userData['password']);
            $data['status'] = 'Enrolled';
        } else {
            $data['first_name'] = $userData['first_name'];
            $data['last_name'] = $userData['last_name'];
            $data['middle_name'] = $userData['middle_name'] ?? '';
            $data['email_address'] = $userData['email_address'];
            $data['username'] = $userData['username'];
            $data['password_hash'] = Hash::make($userData['password']);
            $data['status'] = 'Active';
        }

        $roleModel::create($data);

        return $user;
    }
}