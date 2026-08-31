<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        // Names are real CSS color keywords on purpose - the storefront uses
        // color.name directly as a CSS background-color value (see
        // frontend/src/components/partials/Colors.vue).
        foreach (['Black', 'White', 'Navy', 'Grey', 'Red', 'Olive', 'Beige'] as $name) {
            Color::firstOrCreate(['name' => $name]);
        }
    }
}
