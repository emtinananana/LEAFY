<?php

namespace App\Http\Controllers\customer\profile;
use App\Models\customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class customerprofilecontroller extends Controller
{
    public function update(Request $request)
    {
        $this->validate($request, [
           
            'name'=> 'required',
            'email' => "email|unique:customers,email,".auth()->guard('customer-api')->user()->id,
            'phone' => "unique:customers,phone,".auth()->guard('customer-api')->user()->id,
            'password' => 'min:6',
            'address'=>'required'

        
        ]);
        $customer = auth()->guard('customer-api')->user();
        $customer->update([
           
            'name' => $request->name ? $request->name : $customer->name,
            'email' => $request->email ? $request->email : $customer->email,
            'phone' => $request->phone ? $request->phone : $customer->phone,
            'password' => $request->password ? bcrypt($request->password) : $customer->password,
            'address' => $request->address ? ($request->address) : $customer->address,
          ]);
          return response()->json([

            
           'message' => 'profile updated successfully',
           'customer'=> $customer,
       ], 201);
       
        
        }
        public function updateAvatar(Request $request)
        {
            
      
             if (!$request->user('customer-api')) {
                return response()->json([
                 'message' => 'User not authenticated',
              ], 401);
             }
        
         
            $this->validate($request, [
                'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        
           
            if (!$request->hasFile('avatar')) {
                return response()->json([
                    'message' => 'No file uploaded',
                ], 400);
            }
        
           
            $customer = auth()->guard('customer-api')->user();
            if (!$customer) {
                return response()->json([
                    'message' => 'Authentication failed',
                ], 401);
            }
    
            if ($customer->avatar) {
                $oldAvatarPath = public_path('uploads/customer/avatars/' . $customer->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->extension();
            $avatar->move(public_path('uploads/customer/avatars'), $avatarName);

            $customer->update([
                'avatar' => $avatarName,
            ]);
        
            return response()->json([
                'message' => 'Profile updated successfully',
                'customer' => $customer,
            ], 200);
        }
        
            
             
   
}