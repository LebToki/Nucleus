<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegationComment extends Model
{
    protected $fillable = [
        'delegation_id',
        'user_id',
        'comment',
    ];

    public function delegation(): BelongsTo
    {
        return $this->belongsTo(Delegation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
