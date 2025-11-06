<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_can_create_product(): void
    {
        $category = Category::factory(5)->create();
        $response = $this->post('/api/v1/products',[
            "name" => fake()->word(),
            "description" => fake()->text(),
            "price" => fake()->numberBetween(300,700),
            "quantity" => fake()->numberBetween(20,60),
            "status" => fake()->randomElement(["active","inactive"],),
            "categories_ids" => [1,2],
        ], $this->acquire_token("admin"));
        $response->assertStatus(201);
    }

    public function test_customer_cannot_create_product(): void
    {
        $response = $this->post('/api/v1/products',[
            "name" => fake()->word(),
            "description" => fake()->text(),
            "price" => fake()->numberBetween(300,700),
            "quantity" => fake()->numberBetween(20,60),
            "status" => fake()->randomElement(["active","inactive"])
        ],$this->acquire_token("customer"));
        $response->assertStatus(403);
    }

    public function test_products_can_be_filtered_by_category(){

        $category = Category::create([
            "name" => fake()->word(),
        ]);
        $product1 = Product::create([
            "name" => fake()->word(),
            "description" => fake()->text(),
            "price" => fake()->numberBetween(300,700),
            "quantity" => fake()->numberBetween(20,60),
            "status" => "active"
        ]);
        $product2  = Product::create([
            "name" => fake()->word(),
            "description" => fake()->text(),
            "price" => fake()->numberBetween(300,700),
            "quantity" => fake()->numberBetween(20,60),
            "status" => "active"
        ]);
        echo Product::all()->count();
        $product1->categories()->attach($category->id);

        $response = $this->get("/api/v1/products?category=$category->name",$this->acquire_token("customer"));

        $response->assertStatus(200)
            ->assertJsonCount(1,"data");
    }

    private function acquire_token($role){
        $role = Role::create([
            "name" => $role,
        ]);

        $user =  User::create([
            "name" => "custumer",
            "email" => "customer2@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

        $token = $user->createToken("customer")->plainTextToken;

        return  [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ];
    }
    
 
}
