<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProductLot;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => 'owner',
                'is_superuser' => true,
                'password' => Hash::make('admin123'),
            ],
        );

        $officialLots = [
            ['shape' => 'Jurra', 'grade' => 'Double Super', 'quantity' => 0.2, 'rate' => 33000, 'amount' => 6600],
            ['shape' => 'Jurra', 'grade' => 'Super', 'quantity' => 0.774, 'rate' => 27000, 'amount' => 20898],
            ['shape' => 'Murri', 'grade' => 'Super', 'quantity' => 0.2, 'rate' => 27000, 'amount' => 5400],
            ['shape' => 'Murri Big', 'grade' => 'Super', 'quantity' => 0.0495, 'rate' => 27000, 'amount' => 1336.50],
            ['shape' => 'Jurra', 'grade' => 'A+', 'quantity' => 0.5355, 'rate' => 25500, 'amount' => 13655.25],
            ['shape' => 'Salla', 'grade' => 'A+', 'quantity' => 0.475, 'rate' => 24000, 'amount' => 11400],
            ['shape' => 'K. Murri', 'grade' => 'A+', 'quantity' => 0.501, 'rate' => 30000, 'amount' => 15030],
            ['shape' => 'Jurra', 'grade' => 'A', 'quantity' => 1.5, 'rate' => 24000, 'amount' => 36000],
            ['shape' => 'Murri', 'grade' => 'A', 'quantity' => 0.5, 'rate' => 22500, 'amount' => 11250],
            ['shape' => 'Baby Salla', 'grade' => 'A', 'quantity' => 0.084, 'rate' => 22500, 'amount' => 1890],
            ['shape' => 'K. Murri', 'grade' => 'B+', 'quantity' => 0.1415, 'rate' => 18000, 'amount' => 2547],
            ['shape' => 'Jurra', 'grade' => 'B+', 'quantity' => 1.073, 'rate' => 15000, 'amount' => 16095],
            ['shape' => 'Salla', 'grade' => 'B+', 'quantity' => 0.147, 'rate' => 13500, 'amount' => 1984.50],
            ['shape' => 'Murri Small', 'grade' => 'B', 'quantity' => 0.136, 'rate' => 15000, 'amount' => 2040],
            ['shape' => 'Jurra', 'grade' => 'C', 'quantity' => 0.572, 'rate' => 10500, 'amount' => 6006],
        ];

        foreach ($officialLots as $lot) {
            $business = match (true) {
                in_array($lot['grade'], ['Double Super', 'Super'], true) => ['Al-Riyassi', 'الرئاسي', 'al_riyassi'],
                in_array($lot['grade'], ['A+', 'A'], true) => ['Al-Nader', 'النادر', 'al_nader'],
                in_array($lot['grade'], ['B+', 'B'], true) => ['Al-Safwah', 'الصفوة', 'al_safwah'],
                default => ['Al-Naqwah', 'النقوة', 'al_naqwah'],
            };

            $product = Product::updateOrCreate(
                ['product_key' => $business[2] . '_' . strtolower(str_replace(['+', ' '], ['plus', '_'], $lot['grade'])) . '_' . strtolower(str_replace(' ', '_', $lot['shape']))],
                [
                    'business_name' => $business[0],
                    'business_name_ar' => $business[1],
                    'grade' => $lot['grade'],
                    'distributor_shape' => $lot['shape'],
                    'active' => true,
                ],
            );

            ProductLot::updateOrCreate(
                ['source_reference' => 'GCC-BLR-01', 'distributor_shape' => $lot['shape'], 'grade' => $lot['grade'], 'quantity_kg' => $lot['quantity']],
                [
                    'product_id' => $product->id,
                    'source_date' => '2026-02-01',
                    'business_name' => $business[0],
                    'business_name_ar' => $business[1],
                    'supplier_rate_per_kg' => $lot['rate'],
                    'supplier_amount' => $lot['amount'],
                    'working_cost_rate' => 0.125,
                    'target_margin_rate' => in_array($lot['grade'], ['A+', 'A'], true) ? 0.40 : 0.25,
                ],
            );
        }
    }
}
