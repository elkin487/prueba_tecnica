<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Configuración de habitaciones de un hotel: cantidad de habitaciones
 * de un tipo y una acomodación determinados.
 */
class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'accommodation_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /** Hotel al que pertenece esta configuración. */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** Tipo de habitación de esta configuración. */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /** Acomodación de esta configuración. */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }
}
