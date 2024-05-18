<?php

namespace App\Http\Controllers\admin\profile;
use App\Models\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class profileController extends Controller

    {
        public function update(Request $request)
        {
            $this->validate($request, [
               
               
                'email'=> 'required',
                 'password' => 'min:6',
              
                ]);
            $admin = auth()->guard('admin-api')->user();
            $admin->update([  'email' => $request->email ? ($request->email) : $admin->email,
                'password' => $request->password ? bcrypt($request->password) : $admin->password,
       

              ]);
            }
            public function updateAvatar(Request $request)
            {
                if (!$request->user('admin')) {
                    return response()->json([
                        'message' => 'User not authenticated',
                    ], 401);
                }
                $this->validate($request, [
                    'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]);
                $admin = auth()->guard('admin')->user();
              
                if($admin->avatar) {
                  
                    unlink(public_path('uploads/admins/avatars/'.$admin->avatar));
                }
                
                $avatar = $request->file('avatar');
              
                $avatarName = time() . '.' . $avatar->extension();
              
                $avatar->move(public_path('uploads/admins/avatars'), $avatarName);
              
                $admin->update([
                    'avatar' => $avatarName,
                ]);
              
                return response()->json([
                    'message' => 'Profile updated successfully',
                    'admin' => $admin,
                ], 200);
            
          return response()->json([
              'message' => 'Profile updated successfully',
          ],200);
      }
  }
  
  
