<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Product::with("categories")->filter($request);
        $data = $data->paginate(20);
        
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = DB::transaction(function () use($request){
            $category_ids = $request->validated("categories_ids");
            $product = Product::create([
                "name" => $request->name,
                "description" => $request->description,
                "quantity" => $request->quantity,
                "price" => $request->price,
                "status" => $request->status,
            ]);
            $product->categories()->attach($category_ids);
            $product->load("categories");
            return $product;
        });
        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
       
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update(
            $request->validated()
        );
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
    }
}
