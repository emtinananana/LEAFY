<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftDetail extends Model
{
    protected $fillable = ['recipient_name', 'recipient_phone', 'recipient_address'];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }
}
