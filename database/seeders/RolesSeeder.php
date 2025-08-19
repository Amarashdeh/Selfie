<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Admin roles (guard admin)
        Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'admin']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);

        // User roles (guard web)
        Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Moderator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Parent', 'guard_name' => 'web']);
    }
}
