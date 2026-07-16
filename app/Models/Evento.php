<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table = 'evento';

    protected $fillable = [
        'circulo_id',
        'tipo',
        'titulo',
        'descripcion',
        'fecha_hora',
        'ubicacion',
        'responsable_id',
        'estado',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function circulo(): BelongsTo
    {
        return $this->belongsTo(Circulo::class, 'circulo_id');
    }

    /** Encargado del turno de comida (puede ser null) */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_id');
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'evento_id');
    }
}
