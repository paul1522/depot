<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected static $unguarded = true;

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
