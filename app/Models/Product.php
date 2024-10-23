<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
        'created_by'
    ];

    protected $casts = [
        'price' => 'float',
        'stock_quantity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->created_by = auth()->id();
        });
    }

    public function scopeSearch($query, $name)
    {
        return $query->when($name, function ($query) use($name){
            return $query->where('name', 'like', "%{$name}%");
        });
    }

    public function scopePriceRange($query, $min, $max)
    {
        if (!is_null($min) && $min !== '') {
            $query->where('price', '>=', (float) $min);
        }

        if (!is_null($max) && $max !== '') {
            $query->where('price', '<=', (float) $max);
        }

        return $query;
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
        ->withPivot('total_price', 'quantity')
        ;
    }
}
