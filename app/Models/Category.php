<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    public $guarded = [];


    protected static function boot()
    {
        parent::boot();

             static::created(function ($product) {
            $product->slug = Str::slug($product->name . '-' . $product->id);
            $product->save();
        });
        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name . '-' . $product->id);
            }
        });
    }
}
