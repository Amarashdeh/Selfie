<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run()
    {
        // Create roles (admin and superadmin)
        $roles = ['SuperAdmin', 'Admin'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'admin']); // For admin guard
        }


        // Create default admin user
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),  // Change password for production
            ]
        );

        // Assign admin role with 'admin' guard
        $adminRole = Role::where('name', 'SuperAdmin')->where('guard_name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }
}
