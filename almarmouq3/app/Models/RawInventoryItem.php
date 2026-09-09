<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_box_code',
        'item_index',
        'variety_cut',
        'supplier_grade',
        'tier_class',
        'weight_kg',
        'base_rate_aed',
        'landed_cost_aed',
        'margin_pct',
        'selling_price_per_kg',
    ];
}
