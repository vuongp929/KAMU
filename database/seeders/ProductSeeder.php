<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        for ($i = 1; $i <= 10; $i++) {
            // Tạo hoặc cập nhật sản phẩm
            $product = Product::updateOrCreate(
                ['code' => 'SP' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => 'Sản phẩm ' . $i,
                ]
            );

            // Tạo 3 biến thể cho mỗi sản phẩm
            for ($j = 1; $j <= 3; $j++) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => 'Size ' . ['S', 'M', 'L'][$j - 1],
                    'price' => rand(100000, 500000),
                    'stock' => rand(10, 50),
                ]);
            }

            // Liên kết sản phẩm với 1 hoặc nhiều danh mục ngẫu nhiên
            $randomCategories = $categories->random(rand(1, 3)); // Chọn ngẫu nhiên từ 1 đến 3 danh mục
            foreach ($randomCategories as $category) {
                $product->categories()->attach($category->id);
            }
        }
    }
}
