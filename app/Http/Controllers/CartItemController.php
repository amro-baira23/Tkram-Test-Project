<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return request()->user()->orderitems()->paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request)
    {
           DB::transaction(function () use ($request) {
            $order_item_ids = [];
            foreach($request->order_items as $item){
                 $orderItem = OrderItem::create([
                    "product_id" => $item["product_id"],
                    "quantity" => $item["quantity"],
                    "price" => $item["price"],
                ]);
                $order_item_ids[] = $orderItem->id;
            }
            $request->user()->orderItems()->attach($order_item_ids);
        });
        return $request->user()->orderItems()->get();
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,OrderItem $orderItem)
    {
        $request->user()->orderItems()->detach($orderItem);
    }

    public function destroyAll(Request $request)
    {
        $request->user()->orderItems()->detach();
    }
}
