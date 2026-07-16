<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalificacionCirculo extends Model
{
    protected $table = 'calificacion_circulo';

    // Solo tiene created_at (con default en la BD), no updated_at
    public $timestamps = false;

    protected $fillable = [
        'circulo_id',
        'usuario_id',
        'puntuacion',
        'comentario',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
        'created_at' => 'datetime',
    ];

    public function circulo(): BelongsTo
    {
        return $this->belongsTo(Circulo::class, 'circulo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
