<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                "user_id" => $request->user()->id,
                "order_number" => $request->order_number,
                "total" => 0,
                "status" => "pending",
            ]);
            $total = 0;
            $order_items = $request->user()->orderItems()->get();
            foreach($order_items as $orderItem){
                $total += $orderItem->quantity;
                $orderItem->update([
                    "order_id" => $order->id,
                ]);
                
            }
            $request->user()->orderItems()->detatch();
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
