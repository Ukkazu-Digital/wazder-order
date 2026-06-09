<?php

namespace Database\Seeders;

use App\Models\v2\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Menambahkan 500 produk baru tanpa menghapus data lama
        Product::factory()->count(500)->create()->each(function ($product, $index) {
            
            // Logika: 50% produk baru langsung diberi stok, 50% tidak
            if ($index % 2 === 0) {
                DB::table('stock_entries')->insert([
                    'product_id' => $product->id,
                    'qty_remaining' => rand(10, 100),
                    'purchase_price' => $product->selling_price * 0.7,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}