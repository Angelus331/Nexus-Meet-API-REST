<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gasto extends Model
{
    protected $table = 'gasto';

    // Solo tiene created_at (con default en la BD), no updated_at
    public $timestamps = false;

    protected $fillable = [
        'circulo_id',
        'evento_id',
        'usuario_pagador_id',
        'monto_total',
        'descripcion',
        'fecha',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'fecha' => 'date',
        'created_at' => 'datetime',
    ];

    public function circulo(): BelongsTo
    {
        return $this->belongsTo(Circulo::class, 'circulo_id');
    }

    /** Evento asociado (turno de comida), puede ser null */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function pagador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_pagador_id');
    }

    /** División del gasto entre los miembros */
    public function detalles(): HasMany
    {
        return $this->hasMany(GastoDetalle::class, 'gasto_id');
    }
}
