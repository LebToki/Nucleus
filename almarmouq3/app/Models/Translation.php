<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends Model
{
    protected $fillable = [
        'group',
        'key',
        'locale',
        'value',
        'user_id',
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(TranslationHistory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
