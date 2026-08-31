<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['S', 'M', 'L', 'XL', 'XXL'] as $name) {
            Size::firstOrCreate(['name' => $name]);
        }
    }
}
