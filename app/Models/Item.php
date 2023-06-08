<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected static $unguarded = true;

    public function billOfMaterials(): HasMany
    {
        return $this->hasMany(BillOfMaterials::class, 'master_item_id');
    }
}
