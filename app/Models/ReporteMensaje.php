<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteMensaje extends Model
{
    protected $table = 'reporte_mensaje';

    // Solo tiene created_at (con default en la BD), no updated_at
    public $timestamps = false;

    protected $fillable = [
        'mensaje_id',
        'usuario_id',
        'motivo',
        'resuelto',
    ];

    protected $casts = [
        'resuelto' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function mensaje(): BelongsTo
    {
        return $this->belongsTo(MensajeChat::class, 'mensaje_id');
    }

    /** Usuario que hizo el reporte */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}