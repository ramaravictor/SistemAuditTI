<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'), // password yang di-encrypt
            'role' => 'admin',
            'is_approved' => true,
        ]);
        User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'User Test',
            'password' => bcrypt('password'), // password yang di-encrypt
            'role' => 'user',
            'is_approved' => true,
        ]);
    }
}
