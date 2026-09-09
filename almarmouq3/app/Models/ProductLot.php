<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLot extends Model
{
    protected $fillable = [
        'product_id',
        'source_reference',
        'source_date',
        'distributor_shape',
        'business_name',
        'business_name_ar',
        'grade',
        'quantity_kg',
        'supplier_rate_per_kg',
        'supplier_amount',
        'working_cost_rate',
        'target_margin_rate',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function casts(): array
    {
        return [
            'source_date' => 'date',
            'quantity_kg' => 'decimal:4',
            'supplier_rate_per_kg' => 'decimal:2',
            'supplier_amount' => 'decimal:2',
            'working_cost_rate' => 'decimal:4',
            'target_margin_rate' => 'decimal:4',
        ];
    }
}
