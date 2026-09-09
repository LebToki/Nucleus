<?php

namespace App\Http\Controllers;

use App\Models\RawInventoryItem;
use App\Models\ProductTier;
use Illuminate\Http\Request;

class AgarwoodCatalogController extends Controller
{
    public function index()
    {
        $workingCostPct = 0.125; // 12.5% Working Cost overhead
        $tolaGrams = 11.6638;

        // Raw items from PDF Box GCC-BLR-01
        $rawItems = RawInventoryItem::where('lot_box_code', 'GCC-BLR-01')->orderBy('item_index')->get();

        // 1. Master Telemetry
        $totalWeightKg = $rawItems->sum('weight_kg');
        $acquisitionCapex = $rawItems->sum(fn($i) => $i->weight_kg * $i->base_rate_aed);
        $workingCostAed = $acquisitionCapex * $workingCostPct;
        $totalLandedCost = $acquisitionCapex + $workingCostAed;

        // 2. Compute 4 Tiers dynamically
        $tierSlugs = [
            'al-riyassi-plus' => ['name' => 'Al-Riyassi A+', 'margin' => 0.40, 'teaser' => 'Vintage Sovereign Dehn Al Oud Decant', 'teaser_grams' => 0],
            'al-riyassi' => ['name' => 'Al-Riyassi A', 'margin' => 0.40, 'teaser' => '5g Al-Riyassi A+ Heartwood Sampler', 'teaser_grams' => 5],
            'al-safwah' => ['name' => 'Al-Safwah', 'margin' => 0.25, 'teaser' => '5g Al-Riyassi A Rare Cut Sampler', 'teaser_grams' => 5],
            'al-naqwah' => ['name' => 'Al-Naqwah', 'margin' => 0.25, 'teaser' => '5g Al-Safwah Elite Cut Sampler', 'teaser_grams' => 5],
        ];

        $catalogTiers = [];
        $projectedGrossTurnover = 0;

        foreach ($tierSlugs as $slug => $meta) {
            $itemsInTier = $rawItems->where('tier_class', $meta['name']);
            $weightKg = $itemsInTier->sum('weight_kg');
            $costSum = $itemsInTier->sum(fn($i) => $i->weight_kg * $i->base_rate_aed);
            $avgBaseCostKg = $weightKg > 0 ? $costSum / $weightKg : 0;
            $avgLandedCostKg = $avgBaseCostKg * (1 + $workingCostPct);
            $sellingPriceKg = $avgLandedCostKg / (1 - $meta['margin']);

            $tolaPrice = ($sellingPriceKg / 1000) * $tolaGrams;

            $catalogTiers[] = [
                'slug' => $slug,
                'name' => $meta['name'],
                'weight_kg' => $weightKg,
                'avg_base_cost' => $avgBaseCostKg,
                'avg_landed_cost' => $avgLandedCostKg,
                'margin_pct' => $meta['margin'] * 100,
                'selling_price_kg' => $sellingPriceKg,
                'teaser' => $meta['teaser'],
                'packages' => [
                    '1_tola' => round($tolaPrice),
                    '2_tola' => round($tolaPrice * 2),
                    '3_tola' => round($tolaPrice * 3),
                    'quarter_kg' => round($sellingPriceKg * 0.25),
                    'half_kg' => round($sellingPriceKg * 0.50),
                    'full_kg' => round($sellingPriceKg),
                ]
            ];

            $projectedGrossTurnover += ($weightKg * $sellingPriceKg);
        }

        $projectedGrossProfit = $projectedGrossTurnover - $totalLandedCost;

        return view('modules.vault_atelier.catalog-master', compact(
            'rawItems',
            'catalogTiers',
            'totalWeightKg',
            'acquisitionCapex',
            'workingCostAed',
            'totalLandedCost',
            'projectedGrossTurnover',
            'projectedGrossProfit'
        ));
    }
}
