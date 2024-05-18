<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
    