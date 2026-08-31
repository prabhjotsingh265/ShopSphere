<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Coupon;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\PayOrderRequest;
use App\Http\Resources\UserResource;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function storeUserOrders(StoreOrderRequest $request)
    {
        $data = $request->validated();

        foreach($data['cartItems'] as $item) {
            $order = Order::create([
                'qty' => $item['qty'],
                'user_id' => $request->user()->id,
                'coupon_id' => $item['coupon_id'],
                'total' => $this->calculateEachOrderTotal($item['qty'],$item['price'],$item['coupon_id']),
            ]);
            $order->products()->attach($item['product_id']);
        }
        return response()->json([
            'user' => UserResource::make($request->user())
        ]);
    }

    /**
     * Calculate each order total
     */
    public function calculateEachOrderTotal($qty,$price,$coupon_id)
    {
        $discount = 0;
        $total = $price * $qty;
        $coupon = Coupon::find($coupon_id);

        if($coupon && $coupon->checkIfValid()) {
            $discount = $total * $coupon->discount / 100;
        }

        return $total - $discount;
    }

    
    public function payOrdersByStripe(PayOrderRequest $request)
    {
        $data = $request->validated();

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'ShopSphere'
                        ],
                        'unit_amount' => $this->calculateTotalToPay($data['cartItems'])
                    ],
                    'quantity' => 1
                ]],
                'mode' => 'payment',
                'success_url' => $data['success_url']
            ]);
            //return the link to the stripe checkout form
            return response()->json([
                'url' => $checkout_session->url
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

   
    public function calculateTotalToPay($items)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $this->calculateEachOrderTotal($item['qty'],$item['price'],$item['coupon_id']);
        }
        return (int) round($total * 100);
    }
}
