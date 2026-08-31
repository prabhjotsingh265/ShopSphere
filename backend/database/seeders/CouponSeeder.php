<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            ['name' => 'WELCOME10', 'discount' => 10],
            ['name' => 'SUMMER20', 'discount' => 20],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(
                ['name' => $data['name']],
                [
                    'discount' => $data['discount'],
                    'valid_until' => Carbon::now()->addMonths(3),
                ]
            );
        }
    }
}
