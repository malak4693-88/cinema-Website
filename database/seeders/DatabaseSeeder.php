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
        // Create the admin user if it does not exist, or update it if it already exists.
        User::updateOrCreate(
            // The email is used to find the admin user.
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin',
                // The password is hashed before saving for security.
                'password' => Hash::make('123456'),
            ]
        );
    }
}
