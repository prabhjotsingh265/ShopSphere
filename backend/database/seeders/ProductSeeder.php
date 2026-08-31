<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Thumbnail filenames referenced here must exist at
     * backend/storage/app/public/images/products/{filename}
     * See DOCKER_GUIDE.md / the seeder command output for download links.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Field Tee',
                'category' => 'Men',
                'brand' => 'Carhartt',
                'colors' => ['Black', 'Olive'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'qty' => 40,
                'price' => 32,
                'desc' => '<p>1. Made of 100% heavyweight cotton.<br>2. Neck Type - Crew Neck<br>3. Reinforced seams for durability<br>4. Relaxed, workwear-inspired fit</p>',
                'thumbnail' => 'field-tee.jpg',
            ],
            [
                'name' => 'Classic Crew Tee',
                'category' => 'Men',
                'brand' => 'Nike',
                'colors' => ['White', 'Black'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'qty' => 60,
                'price' => 28,
                'desc' => '<p>1. Made of 100% combed cotton.<br>2. Neck Type - Crew Neck<br>3. Pattern - Solid<br>4. Everyday essential fit</p>',
                'thumbnail' => 'classic-crew-tee.jpg',
            ],
            [
                'name' => 'Ridge Hoodie',
                'category' => 'Men',
                'brand' => 'Champion',
                'colors' => ['Grey', 'Navy'],
                'sizes' => ['M', 'L', 'XL'],
                'qty' => 25,
                'price' => 65,
                'desc' => '<p>1. Made of cotton-poly fleece blend.<br>2. Kangaroo pocket, drawstring hood<br>3. Ribbed cuffs and hem<br>4. Midweight, year-round wear</p>',
                'thumbnail' => 'ridge-hoodie.jpg',
            ],
            [
                'name' => 'Bold Stripe Tee',
                'category' => 'Women',
                'brand' => 'Adidas',
                'colors' => ['Navy', 'White'],
                'sizes' => ['S', 'M', 'L'],
                'qty' => 35,
                'price' => 34,
                'desc' => '<p>1. Made of 95% cotton, 5% elastane.<br>2. Neck Type - Crew Neck<br>3. Pattern - Striped<br>4. Slim, tailored fit</p>',
                'thumbnail' => 'bold-stripe-tee.jpg',
            ],
            [
                'name' => 'Essential Tee',
                'category' => 'Women',
                'brand' => 'Puma',
                'colors' => ['Black', 'Red'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'qty' => 50,
                'price' => 26,
                'desc' => '<p>1. Made of 100% organic cotton.<br>2. Neck Type - Crew Neck<br>3. Pattern - Solid<br>4. Cropped, relaxed fit</p>',
                'thumbnail' => 'essential-tee.jpg',
            ],
            [
                'name' => 'Graphic Print Tee',
                'category' => 'Kids',
                'brand' => 'Nike',
                'colors' => ['Red', 'White'],
                'sizes' => ['S', 'M'],
                'qty' => 30,
                'price' => 22,
                'desc' => '<p>1. Made of 100% soft cotton.<br>2. Neck Type - Crew Neck<br>3. Pattern - Graphic Print<br>4. Machine washable</p>',
                'thumbnail' => 'graphic-print-tee.jpg',
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();
            $brand = Brand::where('name', $data['brand'])->first();

            if (!$category || !$brand) {
                continue;
            }

            $product = Product::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'qty' => $data['qty'],
                    'price' => $data['price'],
                    'desc' => $data['desc'],
                    'thumbnail' => 'storage/images/products/' . $data['thumbnail'],
                    'status' => 1,
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                ]
            );

            $colorIds = Color::whereIn('name', $data['colors'])->pluck('id');
            $sizeIds = Size::whereIn('name', $data['sizes'])->pluck('id');
            $product->colors()->syncWithoutDetaching($colorIds);
            $product->sizes()->syncWithoutDetaching($sizeIds);
        }
    }
}
