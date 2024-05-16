<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_type', 'name', 'description', 'price', 'quantity'];

    function incrementLikeCount()
    {
        $this->increment('likecount');
    }

    function decrementLikeCount()
    {
        $this->decrement('likecount');
    }
    function ProductType()
    {
        return $this->belongsTo(ProductType::class, 'product_type', 'name');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
