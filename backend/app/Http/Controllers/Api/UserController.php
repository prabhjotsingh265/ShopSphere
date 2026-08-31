<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthUserRequest;
use App\Http\Requests\DeleteUserAccountRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        if($request->validated()) {
            User::create($request->validated());
            return response()->json([
                'message' => 'Account created successfully'
            ]);
        }
    }

    
    public function auth(AuthUserRequest $request) 
    {
        if($request->validated()) {
            $user = User::whereEmail($request->email)->first();
            if(!$user || !Hash::check($request->password,$user->password)) {
                return response()->json([
                    'error' => 'These credentials do not match any of our records.'
                ]);
            }else {
                return response()->json([
                    'user' => UserResource::make($user),
                    'access_token' => $user->createToken('new_user')->plainTextToken,
                    'message' => 'Logged in successfully'
                ]);
            }
        }
    }

    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    
    public function UpdateUserProfile(Request $request)
    {
        $request->validate([
            'profile_image' => 'image|mimes:png,jpg,jpeg,webp|max:2048'
        ]);

        if($request->hasFile('profile_image')) {
            //check if the old image exists and remove it
            if(File::exists(public_path($request->user()->profile_image))) {
                File::delete(public_path($request->user()->profile_image));
            }
            //store the user profile image 
            $file = $request->file('profile_image');
            $profile_image_name = time().'_'.$file->getClientOriginalName();
            $file->storeAs('images/users',$profile_image_name,'public');
            //update the user profile image
            $request->user()->update([
                'profile_image' => 'storage/images/users/'.$profile_image_name
            ]);
            //return the response
            return response()->json([
                'user' => UserResource::make($request->user()),
                'message' => 'Profile image has been updated successfully'
            ]);
        }else {
            $request->user()->update([
                'name' => $request->name,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'zip_code' => $request->zip_code,
                'phone_number' => $request->phone_number,
                'profile_completed' => 1
            ]);
            //return the response
            return response()->json([
                'user' => UserResource::make($request->user()),
                'message' => 'Profile updated successfully'
            ]);
        }
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $request->user()->update([
            'password' => $request->password
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    public function deleteAccount(DeleteUserAccountRequest $request)
    {
        $user = $request->user();

        //remove the profile image if one was ever uploaded
        if($user->profile_image && File::exists(public_path($user->profile_image))) {
            File::delete(public_path($user->profile_image));
        }

        //revoke every access token before deleting - orders/reviews cascade via
        //the foreign keys already set up on those tables
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Your account has been deleted'
        ]);
    }
}
