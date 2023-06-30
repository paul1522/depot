<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected static $unguarded = true;

    protected $casts = [
        'date' => 'date',
    ];

    public function item_location(): BelongsTo
    {
        return $this->belongsTo(ItemLocation::class);
    }
}
