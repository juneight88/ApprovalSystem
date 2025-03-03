<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Check if superadmin already exists
        if (!User::where('username', 'superadmin')->exists()) {
            User::create([
                'username' => 'superadmin',
                'password' => Hash::make('admin123'),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'role' => 'super_admin',
                'department' => null,
                'setup_complete' => true
            ]);
        }
    }
}
