<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
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
            "name" => "admin",
        ]);

        User::create([
            "name" => "super_admin",
            "email" => "admin@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

         $role = Role::create([
            "name" => "customer",
        ]);

        User::create([
            "name" => "custumer",
            "email" => "customer@example.com",
            "password" => Hash::make("password123"),
            "role_id" => $role->id,
        ]);

        Category::factory(5)->create();
        Product::factory(20)->create();
        Order::factory(20)->create();
        OrderItem::factory(100)->create();

    }
}
