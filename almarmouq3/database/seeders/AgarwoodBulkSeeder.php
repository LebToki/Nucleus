<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawInventoryItem;

class AgarwoodBulkSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [1, 'Jurra', 'Double Super', 0.2000, 33000, 'Al-Riyassi A+'],
            [2, 'Jurra', 'Super', 0.7740, 27000, 'Al-Riyassi A+'],
            [3, 'Murri', 'Super', 0.2000, 27000, 'Al-Riyassi A+'],
            [4, 'Murri Big', 'Super', 0.0495, 27000, 'Al-Riyassi A+'],
            [5, 'Jurra', 'A+', 0.5355, 25500, 'Al-Riyassi A'],
            [6, 'Sulla', 'A+', 0.4750, 24000, 'Al-Riyassi A'],
            [7, 'K. Murri', 'A+', 0.5010, 30000, 'Al-Riyassi A'],
            [8, 'Jurra', 'A', 1.5000, 24000, 'Al-Riyassi A'],
            [9, 'Murri', 'A', 0.5000, 22500, 'Al-Riyassi A'],
            [10, 'Baby Sulla', 'A', 0.0840, 22500, 'Al-Riyassi A'],
            [11, 'K. Murri', 'B+', 0.1415, 18000, 'Al-Safwah'],
            [12, 'Jurra', 'B+', 1.0730, 15000, 'Al-Safwah'],
            [13, 'Sulla', 'B+', 0.1470, 13500, 'Al-Safwah'],
            [14, 'Murri Small', 'B', 0.1360, 15000, 'Al-Safwah'],
            [15, 'Jurra', 'C', 0.5720, 10500, 'Al-Naqwah'],
        ];

        foreach ($rows as $r) {
            $baseRate = $r[4];
            $landed = $baseRate * 1.125;
            $margin = in_array($r[5], ['Al-Riyassi A+', 'Al-Riyassi A']) ? 40.00 : 25.00;
            $sellingKg = $landed / (1 - ($margin / 100));

            RawInventoryItem::updateOrCreate(
                [
                    'lot_box_code' => 'GCC-BLR-01',
                    'item_index' => $r[0],
                ],
                [
                    'variety_cut' => $r[1],
                    'supplier_grade' => $r[2],
                    'weight_kg' => $r[3],
                    'base_rate_aed' => $baseRate,
                    'landed_cost_aed' => $landed,
                    'margin_pct' => $margin,
                    'selling_price_per_kg' => $sellingKg,
                    'tier_class' => $r[5],
                ]
            );
        }
    }
}
