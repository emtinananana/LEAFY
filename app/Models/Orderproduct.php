<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'orderproducts';
    protected $fillable = ['quantity', 'price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function giftDetails()
    {
        return $this->hasOne(GiftDetail::class);
    }
}
//price
// $orderProducts = OrderProduct::with('product')->get();

// foreach ($orderProducts as $orderProduct) {
//     $productPrice = $orderProduct->product->price;
//     // Do something with the product price
// }
