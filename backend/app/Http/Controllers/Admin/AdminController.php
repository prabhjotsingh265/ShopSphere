<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\AuthAdminRequest;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index()
    {
        $todayOrders = Order::whereDay('created_at',Carbon::today())->get();
        $yesterdayOrders = Order::whereDay('created_at',Carbon::yesterday())->get();
        $monthOrders = Order::whereMonth('created_at',Carbon::now()->month)->get();
        $yearOrders = Order::whereYear('created_at',Carbon::now()->year)->get();

        return view('admin.dashboard')->with([
            'todayOrders' => $todayOrders,
            'yesterdayOrders' => $yesterdayOrders,
            'monthOrders' => $monthOrders,
            'yearOrders' => $yearOrders
        ]);
    }

    public function login()
    {
        if(!auth()->guard('admin')->check()) {
            return view('login');
        }
        return redirect()->route('admin.index');
    }

    public function auth(AuthAdminRequest $request)
    {
        if($request->validated()) {
            if(auth()->guard('admin')->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                $request->session()->regenerate();
                return redirect()->route('admin.index');
            }else {
                return redirect()->route('admin.login')->with([
                    'error' => 'These credentials do not match any of our records.'
                ]);
            }
        }
    }

    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function editProfile()
    {
        return view('admin.profile.edit')->with([
            'admin' => auth()->guard('admin')->user()
        ]);
    }

    public function updateProfile(UpdateAdminProfileRequest $request)
    {
        $admin = auth()->guard('admin')->user();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        //only touch the password if the admin actually typed a new one
        if($request->filled('password')) {
            $data['password'] = $request->password;
        }

        //only touch the image if a new file was actually uploaded
        if($request->hasFile('profile_image')) {
            if($admin->profile_image && File::exists(public_path($admin->profile_image))) {
                File::delete(public_path($admin->profile_image));
            }
            $file = $request->file('profile_image');
            $image_name = time().'_'.$file->getClientOriginalName();
            $file->storeAs('images/admins', $image_name, 'public');
            $data['profile_image'] = 'storage/images/admins/'.$image_name;
        }

        $admin->update($data);

        return redirect()->route('admin.profile.edit')->with([
            'success' => 'Profile updated successfully'
        ]);
    }
}
