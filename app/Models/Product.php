<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function categories(): BelongsToMany{
        return $this->belongsToMany(Category::class,"category_products","product_id","category_id");
    }

    public function scopeActive($query) { 
        return $query->where('status', 'active'); 
    } 
 
    public function scopeInStock($query) { 
        return $query->where('quantity', '>', 0); 
    }
}
