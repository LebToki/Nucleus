<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelegationStatus extends Model
{
    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'color',
        'sort_order',
        'is_default',
        'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }

    public function displayName(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
