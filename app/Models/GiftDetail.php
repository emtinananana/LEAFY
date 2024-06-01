<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftDetail extends Model
{
    protected $table = 'giftdetails';
    protected $fillable = ['order_product_id',
    'order_id',
    'recipient_name',
    'recipient_phone',
    'recipient_address',
    'note'];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
