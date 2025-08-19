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

        // Create default admin user
        $admin = Admin::firstOrCreate(
            ['email' => 'superadmin@example.com'],
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


        // Create default admin user
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),  // Change password for production
            ]
        );

        // Assign admin role with 'admin' guard
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }
}
