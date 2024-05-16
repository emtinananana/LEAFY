<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'deliver_date',
        'status',


    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function history()
    {
        return $this->belongsTo(History::class);
    }
}
