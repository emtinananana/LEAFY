<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;
    protected $guard = 'customer';
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',

    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];  
    public function shoppingCart()
    {
        return $this->hasOne(ShoppingCart::class);
    }
    public function likedProducts()
    {
        return $this->belongsToMany(Product::class, 'customers_likes');
    }
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'posts_likes');
    }
    public function history()
    {
        return $this->hasOne(History::class);
    }
}
