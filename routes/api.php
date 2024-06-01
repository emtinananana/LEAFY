<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\productType\CRUDController;
use App\Http\Controllers\admin\products\ProductController;
use App\Http\Controllers\admin\products\ProductImageController;
use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\TagController;
use App\Http\Controllers\admin\profile\profileController;
use App\Http\Controllers\customer\auth\customerAuthController;
use App\Http\Controllers\customer\profile\customerprofilecontroller;
use App\Http\Controllers\customer\cart\shoppingCartController;
use App\Http\Controllers\customer\cart\CheckOutCart;
use App\Http\Controllers\customer\favs\LikeProductsController;
use App\Http\Controllers\customer\history\HistoryController;

// admin route 
Route::group(['prefix' => 'admin/'], function() {
    Route::post('login', [AuthController::class, 'login'])->name('admin.login');


});
Route::group(['middleware'=>['auth:admin-api'], 'prefix' => 'admin/'], function() {
    //auth
         Route::post('logout', [AuthController::class, 'logout'])->name('logout');
          Route::put('/profile/edit', [profileController::class,'update']);
        Route::post('profile/update/avatar', [profileController::class, 'updateAvatar'])->name('profile.update.avatar');
         // Product Types Routes
         Route::get('productTypes', [CRUDController::class, 'index']);
         Route::get('productTypes/search/{name}', [CRUDController::class, 'search']);
         Route::get('productTypes/{id}', [CRUDController::class, 'show']);
         Route::post('productTypes/add', [CRUDController::class, 'store']);
         Route::put('productTypes/update/{id}', [CRUDController::class, 'update']);
         Route::delete('productTypes/{id}', [CRUDController::class, 'destroy']);
 
         // Products Routes
         Route::get('products', [ProductController::class, 'index']);
         Route::get('products/search/{name}', [ProductController::class, 'search']);
         Route::get('products/{id}', [ProductController::class, 'show']);
         Route::post('products/add', [ProductController::class, 'store']);
         Route::put('products/{id}', [ProductController::class, 'update']);
         Route::delete('products/{id}', [ProductController::class, 'destroy']);
         //productimages 
         Route::post('/products/{productId}/images', [ProductImageController::class, 'store']);
         Route::delete('/products/{productId}/images/{imageId}', [ProductImageController::class, 'destroy']);
         Route::put('/products/{productId}/images/{imageId}', [ProductImageController::class, 'update']);
         //tags
       
       Route::get('tags/index', [TagController::class, 'index']);
       Route::post('tags/add', [TagController::class, 'store']);
       Route::get('tags/{id}', [TagController::class, 'show']);
       Route::put('tags/{id}', [TagController::class, 'update']);
       Route::delete('tags/{id}', [TagController::class, 'destroy']);
  
        });
        //customer route
Route::group(['prefix' => 'customer/'], function() {
    Route::post('/register', [customerAuthController::class,'register']);
    Route::post('/login', [customerAuthController::class, 'login'])->name('login');
 
});

Route::group(['middleware'=>['auth:customer-api'], 'prefix' => 'customer/'], function() {
    Route::post('/logout', [customerAuthController::class, 'logout'])->name('logout');
    Route::put('/profile/edit', [customerprofilecontroller::class,'update']);
    Route::post('profile/update/avatar', [customerprofilecontroller::class, 'updateAvatar'])->name('profile.update.avatar');
    //shopping cart routes
    Route::get('/cart/{id}', [shoppingCartController::class,'show']);
    Route::post('cart/add/{customerId}/{productId}',[shoppingCartController::class,'addToCart']);
    Route::delete('cart/remove/{customerId}/{cartItemId}',[shoppingCartController::class,'removeFromCart']);
    //checkout
    Route::post('cart/checkout', [CheckOutCart::class, 'CheckoutCart']);
    
    //favproducts routes 
    Route ::post('/products/{productId}/like', [LikeProductsController::class, 'likeProduct']);
    Route ::post('/products/{productId}/unlike', [LikeProductsController::class, 'UnlikeProduct']);
    Route ::get('/products/favorites', [LikeProductsController::class, 'ShowLikedProducts']);
    //historyroutes
    Route::get('history', [HistoryController::class, 'showHistory']);




});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
