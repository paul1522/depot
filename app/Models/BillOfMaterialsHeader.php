<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillOfMaterialsHeader extends Model
{
    protected static $unguarded = true;

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    public function details(): HasMany
    {
        return $this->hasMany(BillOfMaterialsDetail::class);
    }
}
