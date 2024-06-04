<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'delivery_date',
        'status',
        'customer_id',
        'order_date',
        'total',

    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function history()
    {
        return $this->belongsTo(History::class);
    }
}
