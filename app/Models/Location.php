<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    protected static $unguarded = true;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
