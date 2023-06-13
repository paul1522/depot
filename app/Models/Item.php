<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected static $unguarded = true;

    public function bill_of_materials(): HasMany
    {
        return $this->hasMany(BillOfMaterials::class, 'master_item_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
