<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with("orderItems")
            ->get();

        return $orders;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
         $order_items = $request->user()->orderItems()->get();
        if($order_items->isEmpty()){
            return response([
                "message" => "can't order with empty cart"
            ], 403);
        }

        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                "user_id" => $request->user()->id,
                "order_number" => $request->order_number,
                "total" => 0,
                "status" => "pending",
            ]);
            $total = 0;

            $order_items = $request->user()
                ->orderItems()
                ->with("product")
                ->get();

            foreach($order_items as $orderItem){
                $total += $orderItem->price;
                $product = $orderItem->product;
                $orderItem->update([
                    "order_id" => $order->id,
                ]);
                $product->reduceQuantity($orderItem->quantity);
                
            }
            $request->user()->orderItems()->detach();
            $order->total = $total;
            $order->save();
            return $order;
        });
        $order->load("orderItems");
        return $order;  
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        Gate::authorize("view",$order);
        $order->load("orderItems");
        return $order;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Order $order)
    {
        $order->update([
            "status" => "cancelled"
        ]);
        $order->load("orderItems");
        return $order;
    }

  
}
