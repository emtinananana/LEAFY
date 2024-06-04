<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'quantity',
        'is_gift',
        'product_id'
    
    ];
    protected $casts = [
        'is_gift' => 'boolean',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartItem) {
            $cartItem->is_gift = $cartItem->is_gift ? 1 : 0;
        });
    }

    public function shoppingCart()
    {
        return $this->belongsTo(ShoppingCart::class, );
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    } 


}
