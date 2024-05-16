<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'content',
        'image_url',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
