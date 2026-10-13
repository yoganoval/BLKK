<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // ROLE & PERMISSION
        // =====================================================

        $this->call([
            RolePermissionSeeder::class,
        ]);

        // =====================================================
        // USER ADMIN
        // =====================================================

        $admin = User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'password' => Hash::make('admin123'),
        ]);

        // =====================================================
        // ROLE ADMIN
        // =====================================================

        $admin->assignRole('admin');
    }
}