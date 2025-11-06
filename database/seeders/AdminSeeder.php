<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin role exists, create if not
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full system access']
        );

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'System Administrator',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'admin@mail.com',
                'password' => Hash::make('1234'),
                'email_verified_at' => now(),
            ]
        );

        // Attach admin role if not already attached
        if (!$admin->roles()->where('role_id', $adminRole->role_id)->exists()) {
            $admin->roles()->attach($adminRole->role_id);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@mail.com');
        $this->command->info('Password: 1234');
    }
}
