<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificacion';

    // Solo tiene created_at (con default en la BD), no updated_at
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'circulo_id',
        'tipo',
        'titulo',
        'contenido',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /** Círculo relacionado (puede ser null, ej. notificaciones de sistema) */
    public function circulo(): BelongsTo
    {
        return $this->belongsTo(Circulo::class, 'circulo_id');
    }
}
