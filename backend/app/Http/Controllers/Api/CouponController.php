<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function applyCoupon(Request $request) 
    {
        $coupon = Coupon::whereName($request->name)->first();
        if($coupon && $coupon->checkIfValid()) {
            return response()->json([
                'message' => 'Coupon applied successfully',
                'coupon' => $coupon
            ]);
        }else {
            return response()->json([
                'error' => 'Invalid or expired coupon'
            ]);
        }
    }
}
