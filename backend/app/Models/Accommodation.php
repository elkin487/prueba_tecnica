<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Acomodación del catálogo (Sencilla, Doble, Triple, Cuádruple).
 */
class Accommodation extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /** Tipos de habitación que admiten esta acomodación. */
    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'room_type_accommodation');
    }

    /** Configuraciones de hotel que usan esta acomodación. */
    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }
}
