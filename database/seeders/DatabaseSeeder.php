<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@stock.local',
        ], [
            'name' => 'Stock Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $categories = collect([
            ['name' => 'Electronics', 'description' => 'Devices, gadgets, and accessories'],
            ['name' => 'Office Supplies', 'description' => 'Consumables and office essentials'],
            ['name' => 'Cleaning', 'description' => 'Cleaning and sanitation items'],
        ])->mapWithKeys(function (array $category): array {
            $model = Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']],
            );

            return [$category['name'] => $model];
        });

        $suppliers = collect([
            [
                'name' => 'TechSource Ltd',
                'contact_person' => 'Maya Santos',
                'phone' => '+1 555 0101',
                'email' => 'orders@techsource.example',
                'address' => '17 North Avenue',
            ],
            [
                'name' => 'OfficeHub Trading',
                'contact_person' => 'Liam Patel',
                'phone' => '+1 555 0202',
                'email' => 'sales@officehub.example',
                'address' => '220 Commerce Street',
            ],
        ])->mapWithKeys(function (array $supplier): array {
            $model = Supplier::firstOrCreate(
                ['name' => $supplier['name']],
                [
                    'contact_person' => $supplier['contact_person'],
                    'phone' => $supplier['phone'],
                    'email' => $supplier['email'],
                    'address' => $supplier['address'],
                ],
            );

            return [$supplier['name'] => $model];
        });

        $products = [
            [
                'sku' => 'ELEC-1001',
                'name' => 'Laptop 14-inch',
                'category' => 'Electronics',
                'supplier' => 'TechSource Ltd',
                'unit' => 'pcs',
                'cost_price' => 620.00,
                'selling_price' => 799.00,
                'reorder_level' => 5,
                'current_stock' => 18,
            ],
            [
                'sku' => 'ELEC-2003',
                'name' => 'Wireless Mouse',
                'category' => 'Electronics',
                'supplier' => 'TechSource Ltd',
                'unit' => 'pcs',
                'cost_price' => 11.50,
                'selling_price' => 19.99,
                'reorder_level' => 40,
                'current_stock' => 24,
            ],
            [
                'sku' => 'OFF-3002',
                'name' => 'A4 Copy Paper (500 sheets)',
                'category' => 'Office Supplies',
                'supplier' => 'OfficeHub Trading',
                'unit' => 'ream',
                'cost_price' => 3.20,
                'selling_price' => 5.25,
                'reorder_level' => 30,
                'current_stock' => 66,
            ],
            [
                'sku' => 'CLN-4007',
                'name' => 'Surface Disinfectant 1L',
                'category' => 'Cleaning',
                'supplier' => 'OfficeHub Trading',
                'unit' => 'bottle',
                'cost_price' => 2.80,
                'selling_price' => 4.50,
                'reorder_level' => 20,
                'current_stock' => 12,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'category_id' => $categories[$productData['category']]->id,
                    'supplier_id' => $suppliers[$productData['supplier']]->id,
                    'unit' => $productData['unit'],
                    'cost_price' => $productData['cost_price'],
                    'selling_price' => $productData['selling_price'],
                    'reorder_level' => $productData['reorder_level'],
                    'current_stock' => $productData['current_stock'],
                ],
            );
        }

        $seedMovements = [
            [
                'reference' => 'SEED-IN-001',
                'sku' => 'ELEC-1001',
                'movement_type' => StockMovement::TYPE_STOCK_IN,
                'quantity' => 20,
                'notes' => 'Initial stock entry',
            ],
            [
                'reference' => 'SEED-OUT-002',
                'sku' => 'ELEC-1001',
                'movement_type' => StockMovement::TYPE_STOCK_OUT,
                'quantity' => 2,
                'notes' => 'Sample issued stock',
            ],
            [
                'reference' => 'SEED-IN-003',
                'sku' => 'OFF-3002',
                'movement_type' => StockMovement::TYPE_STOCK_IN,
                'quantity' => 70,
                'notes' => 'Initial stock entry',
            ],
            [
                'reference' => 'SEED-ADJ-004',
                'sku' => 'CLN-4007',
                'movement_type' => StockMovement::TYPE_ADJUSTMENT,
                'quantity' => -3,
                'notes' => 'Opening adjustment after count',
            ],
        ];

        foreach ($seedMovements as $movement) {
            $product = Product::where('sku', $movement['sku'])->first();

            if (! $product) {
                continue;
            }

            StockMovement::updateOrCreate(
                ['reference' => $movement['reference']],
                [
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'movement_type' => $movement['movement_type'],
                    'quantity' => $movement['quantity'],
                    'notes' => $movement['notes'],
                    'moved_at' => now(),
                ],
            );
        }
    }
}
