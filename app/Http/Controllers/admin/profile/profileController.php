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
                'name'=> 'required',
                 'password' => 'min:6',
              
                ]);
            $admin = auth()->guard('admin-api')->user();
            $admin->update([  'email' => $request->email ? ($request->email) : $admin->email,
            'name' => $request->name ? ($request->name) : $admin->name,
                'password' => $request->password ? bcrypt($request->password) : $admin->password,
            ]);

             return response()->json([
              'message' => 'Profile updated successfully',
          ],200);
            }
            public function updateAvatar(Request $request)
            {
                if (!$request->user('admin-api')) {
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
            
                $admin = auth()->guard('admin-api')->user();
                if (!$admin) {
                    return response()->json([
                        'message' => 'Authentication failed',
                    ], 401);
                }
            
               
                if ($admin->avatar) {
                    $oldAvatarPath = public_path('uploads/admins/avatars/' . basename($admin->avatar));
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }
            
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->extension();
                $avatarPath = $avatar->move(public_path('uploads/admins/avatars'), $avatarName);
            
                $avatarUrl = url('uploads/admins/avatars/' . $avatarName);
            
              
                $admin->update([
                    'avatar' => $avatarUrl,
                ]);
            
                return response()->json(['message' => 'Avatar updated successfully', 'avatar' => $avatarUrl]);
            }
            
            
  }
  
  
