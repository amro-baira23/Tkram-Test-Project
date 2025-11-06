<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope_does_not_return_active_products()
    {
        Product::factory(100)->create(["status"=>"inactive"]);
        
        $this->assertTrue(Product::active()->count() == 0);
    }

    public function test_in_stock_product_scope()
    {
        Product::factory(1)->create(["quantity"=> 0]);
        Product::factory(80)->create(["quantity"=> fake()->numberBetween(1,100)]);
        
        $this->assertTrue(Product::inStock()->count() == 80);
    }

    public function test_product_price_accessor()
    {
        Product::factory(1)->create(["price"=> 50 ]);
        echo Product::first()->price;
        $this->assertTrue(Product::first()->price == "50 s.p");
    }

    public function test_product_quantity_reducer()
    {
        $product = Product::factory()->create(["quantity"=> 50 ]);
        $this->assertThrows(fn () => $product->reduceQuantity(51));
    }

    public function test_slug_generator_in_product()
    {
        $product = Product::factory(1000)->create();
        $this->assertEquals(Product::where("slug",null)->count(),0);
    }



    

}
