<?php

namespace App\Models;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public $guarded = [];

    public function product_formatted(): Attribute{
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes["price"] . " s.p"
        );
    }
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

    public function categories(): BelongsToMany{
        return $this->belongsToMany(Category::class,"category_products","product_id","category_id");
    }

    public function reduceQuantity(int $amount){
        $this->quantity -= $amount;
        if($this->quantity<0){
            throw new AuthorizationException("product $this->name is unavailable, please update your cart");

        }
        $this->save();
    }

    public function scopeFilter(Builder $query){
        $query = $query->when(request("category"), function ($query, $category) {
            return $query->whereHas("categories", function($query) use ($category){
                return $query->where("name", "LIKE", '%' . $category . '%');
            });
        })->when(request("price_min"), function ($query, $price_min) {
            return $query->where("price", "<" , $price_min );
        })->when(request("price_max"), function ($query, $price_max) {
            return $query->where("price", ">" , $price_max );
        })->when(request("in_stock"), function ($query, $in_stock) {
            return $query->inStock();
        })->when(request("search"), function ($query, $value) {
            return $query->where("name", "LIKE", '%' . $value . '%');
        });
        $query->active();
        return $query; 
    }

    public function scopeActive($query) { 
        return $query->where('status', 'active'); 
    } 
 
    public function scopeInStock($query) { 
        return $query->where('quantity', '>', 0); 
    }
}
