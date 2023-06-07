<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillOfMaterialsDetail extends Model
{
    protected static $unguarded = true;

    public function header(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterialsHeader::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
