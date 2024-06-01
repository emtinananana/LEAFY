<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'order_id'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function order()
    {
       
        return $this->hasMany(Order::class);
      
    }
}
