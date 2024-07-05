<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\productType\CRUDController;
use App\Http\Controllers\admin\products\ProductController;
use App\Http\Controllers\admin\products\ProductImageController;
use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\TagController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\admin\plants\PlantInstructionsController;
use App\Http\Controllers\admin\orders\OrdersController;
use App\Http\Controllers\admin\profile\profileController;
use App\Http\Controllers\customer\auth\customerAuthController;
use App\Http\Controllers\customer\profile\customerprofilecontroller;
use App\Http\Controllers\customer\cart\shoppingCartController;
use App\Http\Controllers\customer\cart\CheckOutCart;
use App\Http\Controllers\customer\favs\LikeProductsController;
use App\Http\Controllers\customer\history\HistoryController;
use App\Http\Controllers\customer\posts\Postscontroller;
use App\Http\Controllers\customer\posts\CommentController;

//shared routes

// Route::get('uploads/admins/avatars/{filename}', function ($filename) {
//     $path = public_path('uploads/admins/avatars/' . $filename);

//     if (!File::exists($path)) {
//         abort(404);
//     }

//     $file = File::get($path);
//     $type = File::mimeType($path);

//     $response = Response::make($file, 200);
//     $response->header("Content-Type", $type);

//     return $response;
// });

Route::get('products', [ProductController::class, 'index']);
Route::get('products/search/{name}', [ProductController::class, 'search']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('productTypes', [CRUDController::class, 'index']);
Route::get('tags/index', [TagController::class, 'index']);
//catalogs
Route::prefix('catalog')->group(function () {
    Route::get('/', [CatalogController::class, 'index']);
    Route::get('/type/{type}', [CatalogController::class, 'showByType']);
    Route::post('/search', [CatalogController::class, 'search']);
    Route::post('/filter', [CatalogController::class, 'filter']);
});

// admin route 
Route::group(['prefix' => 'admin/'], function() {
Route::post('login', [AuthController::class, 'login'])->name('admin.login');
});

Route::group(['middleware'=>['auth:admin-api'], 'prefix' => 'admin/'], function() {
    //auth
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        //profile
        Route::put('/profile/edit', [profileController::class,'update']);
        Route::post('profile/update/avatar', [profileController::class, 'updateAvatar'])->name('profile.update.avatar');

         // Product Types Routes
    
        Route::get('productTypes/search/{name}', [CRUDController::class, 'search']);
        Route::get('productTypes/{id}', [CRUDController::class, 'show']);
        Route::post('productTypes/add', [CRUDController::class, 'store']);
        Route::put('productTypes/update/{id}', [CRUDController::class, 'update']);
        Route::delete('productTypes/{id}', [CRUDController::class, 'destroy']);

        //orders 
        Route::get('orders', [OrdersController::class, 'showOrders']);
        Route::get('orders/{id}', [OrdersController::class, 'show']);
        Route ::put('order/update/{id}', [OrdersController::class, 'update']);
        Route::get('orders/search/{name}', [OrdersController::class, 'search']);
        Route::delete('orders/{id}', [OrdersController::class, 'destroy']);

         // Products Routes
       

        Route::post('products/add', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);

        //plantinstructions
        Route::get('instructions', [PlantInstructionsController::class, 'index']);
        Route::get('instructions/{id}', [PlantInstructionsController::class, 'show']);
        Route::post('instructions/add/{productid}', [PlantInstructionsController::class, 'store']);
        Route::put('instructions/{id}', [PlantInstructionsController::class, 'update']);
        Route::delete('instructions/{id}', [PlantInstructionsController::class, 'destroy']);

         //productimages 
         Route::get('products/{productId}/images', [ProductImageController::class, 'showAllImages']);
        Route::post('/products/{productId}/images', [ProductImageController::class, 'store']);
       
        Route::delete('/products/{productId}/images/{imageId}', [ProductImageController::class, 'destroy']);
        Route::Post('/products/{productId}/images/{imageId}', [ProductImageController::class, 'update']);

         //tags
      
        Route::post('tags/add', [TagController::class, 'store']);
        Route::get('tags/{id}', [TagController::class, 'show']);
        Route::put('tags/{id}', [TagController::class, 'update']);
        Route::delete('tags/{id}', [TagController::class, 'destroy']);
        });
     


        //customer route
Route::group(['prefix' => 'customer/'], function() {

    //auth
Route::post('/register', [customerAuthController::class,'register']);
Route::post('/login', [customerAuthController::class, 'login'])->name('login');
 
});

Route::group(['middleware'=>['auth:customer-api'], 'prefix' => 'customer/'], function() {
    Route::post('/logout', [customerAuthController::class, 'logout'])->name('logout');

    //profile
    Route::put('/profile/edit', [customerprofilecontroller::class,'update']);
    Route::post('profile/update/avatar', [customerprofilecontroller::class, 'updateAvatar'])->name('profile.update.avatar');

    //shopping cart routes
    Route::get('/cart', [shoppingCartController::class,'show']);
    Route::post('cart/add/{productId}',[shoppingCartController::class,'addToCart']);
    Route::delete('cart/remove/{cartItemId}',[shoppingCartController::class,'removeFromCart']);

    //checkout
    Route::post('cart/checkout', [CheckOutCart::class, 'CheckoutCart']);
    Route::get('/giftproducts', [CheckOutCart::class, 'getGiftProducts']);
    
    //favproducts routes 
    Route ::post('/products/{productId}/like', [LikeProductsController::class, 'likeProduct']);
    Route ::post('/products/{productId}/unlike', [LikeProductsController::class, 'UnlikeProduct']);
    Route ::get('/products/favorites', [LikeProductsController::class, 'ShowLikedProducts']);

    //historyroutes
    Route::get('history', [HistoryController::class, 'showHistory']);
    Route::delete('/orders/{order}', [HistoryController::class, 'cancelOrder']);

    //posts
    Route::Get('/posts', [Postscontroller::class, 'showposts']);
    Route::post('/posts', [Postscontroller::class, 'createPost']);
    Route::get('posts/{content}', [Postscontroller::class, 'search']);
    Route::delete('/post/{id}', [Postscontroller::class, 'destroy']);
    Route::post('/posts/like/{id}', [Postscontroller::class, 'likePost']);
    
    //commentsroutes
    Route::get('/posts/{postId}/comments', [CommentController::class, 'showComments']);
    Route::post('/posts/{postId}/comment', [CommentController::class, 'addComment']);




    
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
