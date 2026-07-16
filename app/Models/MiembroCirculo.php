<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiembroCirculo extends Pivot
{
    protected $table = 'miembro_circulo';

    // Esta tabla sí tiene id propio (no es una pivote "pura" sin id)
    public $incrementing = true;

    // No tiene created_at/updated_at, solo fecha_union con default en la BD
    public $timestamps = false;

    protected $fillable = [
        'circulo_id',
        'usuario_id',
        'rol',
        'estado',
        'fecha_union',
    ];

    protected $casts = [
        'fecha_union' => 'datetime',
    ];

    public function circulo(): BelongsTo
    {
        return $this->belongsTo(Circulo::class, 'circulo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }
}
