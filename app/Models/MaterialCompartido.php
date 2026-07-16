<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialCompartido extends Model
{
    protected $table = 'material_compartido';

    // Solo tiene created_at (con default en la BD), no updated_at
    public $timestamps = false;

    protected $fillable = [
        'circulo_id',
        'usuario_id',
        'nombre_archivo',
        'url_archivo',
        'tipo_archivo',
        'tamano_bytes',
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
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
