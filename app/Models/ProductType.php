<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'producttypes';
    protected $fillable = ['name', 'description'];

    function Product()
    {
        return $this->hasMany(Product::class);
    }
}
