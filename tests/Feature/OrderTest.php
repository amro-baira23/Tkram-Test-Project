<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
     public function test_user_can_make_order(): void
    {
        
        $role = Role::create([
            "name" => "customer",
        ]);

        $user = User::create([
            "name" => "custumer",
            "email" => "customer@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

        $token = $user->createToken("customer")->plainTextToken;
        
        
        $response = $this->post('/api/v1/cart',[
            "order_items" => [
                [
                    "product_id" => Product::factory()->create()->id,
                    "quantity" => fake()->numberBetween(1,20),
                    "price" => fake()->numberBetween(300,700),
                ]
            ],
        ],[
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ]);
        $response->assertStatus(200);

        $response = $this->post('/api/v1/orders',[
            "user_id" => User::first()->id,
            "order_number" => fake()->numberBetween(1,100),
            "total" => fake()->numberBetween(300,700),
            "status" => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled'])
        ]);

        $response->assertStatus(201);
    }

    public function test_order_reduces_product(): void
    {
        
        $headers = $this->acquire_token("customer");
 
        $product= Product::factory()->create();

        $response = $this->post('/api/v1/cart',[
            "order_items" => [
                [
                    "product_id" => $product->id,
                    "quantity" => fake()->numberBetween(1,20),
                    "price" => fake()->numberBetween(300,700),
                ]
            ],
        ],$headers);

        $response->assertStatus(200);

        $old_quantity = $product->quantity;

        $response = $this->post('/api/v1/orders',[
            "order_number" => fake()->numberBetween(1,100),
            "total" => fake()->numberBetween(300,700),
            "status" => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled'])
        ],$headers);

        $response->assertStatus(201);

        $product->refresh();

        $this->assertLessThan($old_quantity,$product->quantity);

    }

    public function test_user_can_view_only_their_orders(): void
    {

        $role = Role::create([
            "name" => "customer",
        ]);

        $user = User::create([
            "name" => "custumer",
            "email" => "customer@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

        $token = $user->createToken("customer")->plainTextToken;
        
        $other_user =  User::create([
            "name" => "custumer",
            "email" => "customer2@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

        $other_user_order =  Order::create([
                "user_id" => $other_user->id,
                "order_number" => 10,
                "total" => 0,
                "status" => "pending",
            ]);;

        $response = $this->get('/api/v1/orders/'. $other_user_order->id,[
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_cancel_pending_order(): void
    {

        $role = Role::create([
            "name" => "customer",
        ]);

        $user =  User::create([
            "name" => "custumer",
            "email" => "customer2@example.com",
            "password" => "password123",
            "role_id" => $role->id,
        ]);

        $token = $user->createToken("customer")->plainTextToken;

        $order =  Order::create([
                "user_id" => $user->id,
                "order_number" => 10,
                "total" => 0,
                "status" => "pending",
            ]);;;

        $response = $this->put("/api/v1/orders/$order->id/cancel",headers: [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ]);

        $response->assertStatus(200);
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


    public function test_cart_calculates_total_correctly(): void
    {
        $headers = $this->acquire_token("customer");
 
        $product= Product::factory(2)->create();

        $price1 = fake()->numberBetween(300,700);
        $price2 = fake()->numberBetween(300,700);

        $response = $this->post('/api/v1/cart',[
            "order_items" => [
                [
                    "product_id" => $product[0]->id,
                    "quantity" => fake()->numberBetween(1,20),
                    "price" => $price1,
                ],[
                    "product_id" => $product[1]->id,
                    "quantity" => fake()->numberBetween(1,20),
                    "price" => $price2,
                ]
            ],
        ],$headers);

        $response->assertStatus(200);


        $response = $this->post('/api/v1/orders',[
            "order_number" => fake()->numberBetween(1,100),
        ],$headers);

        $response->assertStatus(201);

        $total = Order::first()->total;

        echo Order::count() ."\n";
        echo $total ."\n";

        $this->assertEquals($total,$price1 + $price2);

    }
}

