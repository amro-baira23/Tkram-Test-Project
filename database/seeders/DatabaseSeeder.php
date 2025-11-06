<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $role = Role::create([
            "name" => "super_admin",
        ]);

        User::create([
            "first_name" => "Super",
            "last_name" => "Admin",
            "username" => "super_admin1",
            "password" => Hash::make("password123"),
            "email" => "admin@example.com",
            "phone_number" => "0933333333",
            "is_active" => true,
            "role_id" => $role->id,
        ]);
    }
}
