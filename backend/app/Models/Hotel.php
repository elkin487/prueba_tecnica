<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hotel de la compañía con sus datos básicos y tributarios.
 */
class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city_id',
        'nit',
        'number_of_rooms',
    ];

    protected $casts = [
        'number_of_rooms' => 'integer',
    ];

    /** Ciudad a la que pertenece el hotel. */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /** Configuraciones de habitaciones del hotel. */
    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }

    /**
     * Cantidad de habitaciones ya configuradas en el hotel
     * (suma de las cantidades de todas sus configuraciones).
     */
    public function configuredRoomsCount(): int
    {
        return (int) $this->rooms()->sum('quantity');
    }
}
