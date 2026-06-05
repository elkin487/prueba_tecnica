<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tipo de habitación del catálogo (Estándar, Junior, Suite).
 * Cada tipo admite un conjunto de acomodaciones válidas.
 */
class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /** Acomodaciones válidas para este tipo de habitación. */
    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'room_type_accommodation');
    }

    /** Configuraciones de hotel que usan este tipo de habitación. */
    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }
}
