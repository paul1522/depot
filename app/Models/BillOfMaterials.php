<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillOfMaterials extends Model
{
    protected static $unguarded = true;

    public function masterItem(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
