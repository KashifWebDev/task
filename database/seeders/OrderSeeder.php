<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a few sample orders for testing
        $orders = [
            ['total_amount' => 299.99],
            ['total_amount' => 149.50],
            ['total_amount' => 89.99],
            ['total_amount' => 499.99],
            ['total_amount' => 199.99],
        ];

        foreach ($orders as $orderData) {
            Order::createNew($orderData['total_amount']);
        }
    }
}
