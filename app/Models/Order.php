<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->user_id = auth()->id();
        });
    }

    public function scopeMyOrders($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
        ->withPivot('total_price', 'quantity')
        ;
    }

}
