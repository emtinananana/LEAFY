<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantInstruction extends Model
{
    protected $fillable = ['instruction'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->where('product_type', 'plant');
    }
}
