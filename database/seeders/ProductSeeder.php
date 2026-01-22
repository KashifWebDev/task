<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop Computer', 'quantity_per_unit' => '1 unit', 'unit_price' => 1299.99, 'units_in_stock' => 50, 'units_on_order' => 10],
            ['name' => 'Wireless Mouse', 'quantity_per_unit' => '1 unit', 'unit_price' => 29.99, 'units_in_stock' => 200, 'units_on_order' => 50],
            ['name' => 'Mechanical Keyboard', 'quantity_per_unit' => '1 unit', 'unit_price' => 149.99, 'units_in_stock' => 75, 'units_on_order' => 25],
            ['name' => 'USB-C Cable', 'quantity_per_unit' => '1 unit', 'unit_price' => 19.99, 'units_in_stock' => 300, 'units_on_order' => 100],
            ['name' => 'External Hard Drive 1TB', 'quantity_per_unit' => '1 unit', 'unit_price' => 79.99, 'units_in_stock' => 120, 'units_on_order' => 30],
            ['name' => 'Monitor 27"', 'quantity_per_unit' => '1 unit', 'unit_price' => 399.99, 'units_in_stock' => 40, 'units_on_order' => 15],
            ['name' => 'Webcam HD', 'quantity_per_unit' => '1 unit', 'unit_price' => 89.99, 'units_in_stock' => 90, 'units_on_order' => 20],
            ['name' => 'Noise Cancelling Headphones', 'quantity_per_unit' => '1 unit', 'unit_price' => 249.99, 'units_in_stock' => 60, 'units_on_order' => 25],
            ['name' => 'USB Flash Drive 64GB', 'quantity_per_unit' => '1 unit', 'unit_price' => 12.99, 'units_in_stock' => 500, 'units_on_order' => 200],
            ['name' => 'Laptop Stand', 'quantity_per_unit' => '1 unit', 'unit_price' => 49.99, 'units_in_stock' => 150, 'units_on_order' => 40],
            ['name' => 'Gaming Mouse Pad', 'quantity_per_unit' => '1 unit', 'unit_price' => 24.99, 'units_in_stock' => 180, 'units_on_order' => 60],
            ['name' => 'SSD 500GB', 'quantity_per_unit' => '1 unit', 'unit_price' => 69.99, 'units_in_stock' => 100, 'units_on_order' => 35],
            ['name' => 'Docking Station', 'quantity_per_unit' => '1 unit', 'unit_price' => 199.99, 'units_in_stock' => 30, 'units_on_order' => 12],
            ['name' => 'Wireless Charger', 'quantity_per_unit' => '1 unit', 'unit_price' => 34.99, 'units_in_stock' => 140, 'units_on_order' => 45],
            ['name' => 'Bluetooth Speaker', 'quantity_per_unit' => '1 unit', 'unit_price' => 59.99, 'units_in_stock' => 110, 'units_on_order' => 30],
            ['name' => 'Tablet Stand', 'quantity_per_unit' => '1 unit', 'unit_price' => 39.99, 'units_in_stock' => 95, 'units_on_order' => 28],
            ['name' => 'HDMI Cable', 'quantity_per_unit' => '1 unit', 'unit_price' => 14.99, 'units_in_stock' => 250, 'units_on_order' => 80],
            ['name' => 'Laptop Sleeve', 'quantity_per_unit' => '1 unit', 'unit_price' => 27.99, 'units_in_stock' => 160, 'units_on_order' => 50],
            ['name' => 'USB Hub', 'quantity_per_unit' => '1 unit', 'unit_price' => 44.99, 'units_in_stock' => 85, 'units_on_order' => 22],
            ['name' => 'Screen Protector', 'quantity_per_unit' => '1 unit', 'unit_price' => 9.99, 'units_in_stock' => 400, 'units_on_order' => 150],
            ['name' => 'Premium Laptop', 'quantity_per_unit' => '1 unit', 'unit_price' => 2499.99, 'units_in_stock' => 15, 'units_on_order' => 5],
            ['name' => 'Budget Mouse', 'quantity_per_unit' => '1 unit', 'unit_price' => 8.99, 'units_in_stock' => 350, 'units_on_order' => 120],
            ['name' => 'Ergonomic Keyboard', 'quantity_per_unit' => '1 unit', 'unit_price' => 179.99, 'units_in_stock' => 55, 'units_on_order' => 18],
            ['name' => 'Thunderbolt Cable', 'quantity_per_unit' => '1 unit', 'unit_price' => 39.99, 'units_in_stock' => 70, 'units_on_order' => 25],
            ['name' => 'Network Switch 8-Port', 'quantity_per_unit' => '1 unit', 'unit_price' => 89.99, 'units_in_stock' => 45, 'units_on_order' => 15],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'quantity_per_unit' => $product['quantity_per_unit'],
                'unit_price' => $product['unit_price'],
                'units_in_stock' => $product['units_in_stock'],
                'units_on_order' => $product['units_on_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
