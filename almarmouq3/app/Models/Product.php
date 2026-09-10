<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'product_key',
        'business_name',
        'business_name_ar',
        'grade',
        'distributor_shape',
        'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function lots(): HasMany
    {
        return $this->hasMany(ProductLot::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    public function getNameAttribute()
    {
        return $this->business_name . ' ' . $this->grade;
    }
}
