<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Ciudad del catálogo. Un hotel pertenece a una ciudad.
 */
class City extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /** Hoteles ubicados en esta ciudad. */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }
}
