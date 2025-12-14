<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Colgate Toothpaste', 'product_code' => 'CGTHPST', 'stock' => 250, 'price' => 50.00, 'tax_percent' => 8],
            ['name' => 'Dove Soap', 'product_code' => 'DVSOP', 'stock' => 500, 'price' => 25.00, 'tax_percent' => 5],
            ['name' => 'Basmati Rice 1kg', 'product_code' => 'BASMRI1', 'stock' => 100, 'price' => 120.00, 'tax_percent' => 0],
            ['name' => 'Sunflower Oil 1L', 'product_code' => 'SUNOIL1', 'stock' => 80, 'price' => 150.00, 'tax_percent' => 0],
            ['name' => 'Milk 500ml', 'product_code' => 'MILKPCK', 'stock' => 300, 'price' => 30.00, 'tax_percent' => 0],
            ['name' => 'Bread', 'product_code' => 'BREAD', 'stock' => 15, 'price' => 40.00, 'tax_percent' => 0],
            ['name' => 'Eggs (12 pcs)', 'product_code' => 'EGGS12', 'stock' => 50, 'price' => 60.00, 'tax_percent' => 0],
            ['name' => 'Apple (1kg)', 'product_code' => 'APPL1KG', 'stock' => 45, 'price' => 80.00, 'tax_percent' => 0],
            ['name' => 'Banana (dozen)', 'product_code' => 'BANDZ', 'stock' => 30, 'price' => 35.00, 'tax_percent' => 0],
            ['name' => 'Laptop', 'product_code' => 'LAPTOPI', 'stock' => 5, 'price' => 55000.00, 'tax_percent' => 18],
            ['name' => 'Refrigerator', 'product_code' => 'REFRIGE', 'stock' => 3, 'price' => 28000.00, 'tax_percent' => 18],
            ['name' => 'Air Conditioner', 'product_code' => 'AIRCOND', 'stock' => 2, 'price' => 32000.00, 'tax_percent' => 18],
            ['name' => 'Smartphone', 'product_code' => 'SMRTPHN', 'stock' => 15, 'price' => 18000.00, 'tax_percent' => 18],
            ['name' => 'Washing Machine', 'product_code' => 'WASHMAN', 'stock' => 4, 'price' => 25000.00, 'tax_percent' => 18],
            ['name' => 'Tea Leaves 500g', 'product_code' => 'TEALVS5', 'stock' => 120, 'price' => 200.00, 'tax_percent' => 5],
            ['name' => 'Coffee Powder 200g', 'product_code' => 'COFFPW2', 'stock' => 80, 'price' => 250.00, 'tax_percent' => 5],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
