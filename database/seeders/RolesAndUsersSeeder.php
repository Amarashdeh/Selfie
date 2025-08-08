<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Teacher', 'Moderator', 'Parent'];
        
        // Create roles with guard_name = 'web'
        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        // Create demo Teacher user
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Demo Teacher',
                'password' => Hash::make('password123'),
            ]
        );
        $teacherRole = Role::where('name', 'Teacher')->where('guard_name', 'web')->first();
        if ($teacherRole) {
            $teacher->assignRole($teacherRole);
        }

        // Create demo Moderator user
        $moderator = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Demo Moderator',
                'password' => Hash::make('password123'),
            ]
        );
        $moderatorRole = Role::where('name', 'Moderator')->where('guard_name', 'web')->first();
        if ($moderatorRole) {
            $moderator->assignRole($moderatorRole);
        }

        // Create demo Parent user
        $parent = User::firstOrCreate(
            ['email' => 'parent@example.com'],
            [
                'name' => 'Demo Parent',
                'password' => Hash::make('password123'),
            ]
        );
        $parentRole = Role::where('name', 'Parent')->where('guard_name', 'web')->first();
        if ($parentRole) {
            $parent->assignRole($parentRole);
        }
    }
}
