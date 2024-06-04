<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_type', 'name', 'description', 'price', 'quantity','like_count'];

    function incrementLikeCount()
    {
        $this->increment('like_count');
    }

    function decrementLikeCount()
    {
        $this->decrement('like_count');
    }
    function ProductType()
    {
        return $this->belongsTo(ProductType::class, 'product_type', 'name');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function likedByCustomers()
    {
        return $this->belongsToMany(Customer::class, 'customers_likes');
    }
    public function plantInstruction()
    {
        return $this->hasMany(PlantInstruction::class);
    }

}
