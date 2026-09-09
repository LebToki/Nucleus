<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    protected $fillable = [
        'product_id',
        'package_name',
        'weight_kg',
        'unit_price',
        'currency',
        'valid_from',
        'valid_until',
        'source',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:6',
            'unit_price' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
