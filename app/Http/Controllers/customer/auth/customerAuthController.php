<?php

namespace App\Http\Controllers\customer\auth;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\hash;
use Illuminate\Support\Facades\auth;

class customerAuthController extends Controller
{
    public function register(Request $request){

        $fields= $request-> validate([
        'name' => 'required|string' ,
        'email' => 'required|string|unique:customers,email' ,
        'password' => 'required|string' ,
        'phone' => 'required|string|unique:customers,phone' ,
        'address' =>'required|string' ,

]);


$customer = Customer::create([
'name'=> $fields['name'],
'email'=> $fields['email'],
'password'=> bcrypt($fields['password']),
'phone'=> $fields['phone'],
'address'=> $fields['address'],

]);

$token =$customer->createToken('registeredcustomer_token')->plainTextToken;
$shoppingCart = new ShoppingCart();
$customer->shoppingCart()->save($shoppingCart);
$shoppingCart->cartItems()->createMany([
]);
return response()->json([

     'token' => $token,
    'message' => 'customer account created successfully',
     'customer'=> $customer,
], 201);

    }

    public function login(Request $request){
        $this->validate($request,[

            'email'=> 'required',
            'password'=> 'required'

        ]);

        if (!Auth::guard('customer')->attempt(['email' => $request->email,'password' => $request->password])) 
        {
            return response([
                'message' => 'Failed to authenticate',
            ], 401);
        }
        return response([
            'user' => auth()->guard('customer')->user(),
            'token' => auth()->guard('customer')->user()->createToken('customer_token')->plainTextToken,
            'token_type' => 'Bearer',
        ], 200);

    }
    public function logout() {
        Auth::user()->tokens()->delete();
        return response([
            'message' => 'User tokens deleted successfully',
        ], 200);
    }
}

