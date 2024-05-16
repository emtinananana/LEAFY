<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\productType\CRUDController;
use App\Http\Controllers\admin\product\ProductController;
use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\TagController;


// admin route 
Route::group(['prefix' => 'admin/'], function() {
    Route::post('login', [AuthController::class, 'login'])->name('admin.login');


});
Route::group(['middleware'=>['auth:admin-api'], 'prefix' => 'admin/'], function() {
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
        });
        //tags
        Route::get('/admin/tags', [TagController::class, 'index']);
Route::post('/admin/tags', [TagController::class, 'store']);
Route::get('/admin/tags/{id}', [TagController::class, 'show']);
Route::put('/admin/tags/{id}', [TagController::class, 'update']);
Route::delete('/admin/tags/{id}', [TagController::class, 'destroy']);
  

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
