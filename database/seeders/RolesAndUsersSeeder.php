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

        // Create demo users with roles
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            ['name' => 'Demo Teacher', 'password' => Hash::make('password123')]
        );
        $teacher->assignRole('Teacher');

        $moderator = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            ['name' => 'Demo Moderator', 'password' => Hash::make('password123')]
        );
        $moderator->assignRole('Moderator');

        $parent = User::firstOrCreate(
            ['email' => 'parent@example.com'],
            ['name' => 'Demo Parent', 'password' => Hash::make('password123')]
        );
        $parent->assignRole('Parent');
    }
}
