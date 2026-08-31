<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->take(3)->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $sampleReviews = [
            ['title' => 'Great fit and quality', 'body' => 'Fabric feels durable and the sizing was spot on. Would buy again.', 'rating' => 5],
            ['title' => 'Good value', 'body' => 'Solid everyday piece, holds up well after a few washes.', 'rating' => 4],
            ['title' => 'Runs slightly large', 'body' => 'Nice quality but I would size down next time.', 'rating' => 4],
        ];

        foreach ($products as $index => $product) {
            $user = $users[$index % $users->count()];
            $sample = $sampleReviews[$index % count($sampleReviews)];

            Review::firstOrCreate(
                ['product_id' => $product->id, 'user_id' => $user->id],
                [
                    'title' => $sample['title'],
                    'body' => $sample['body'],
                    'rating' => $sample['rating'],
                    'approved' => 1,
                ]
            );
        }
    }
}
