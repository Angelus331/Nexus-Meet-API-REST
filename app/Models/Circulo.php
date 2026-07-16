<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circulo extends Model
{
    protected $table = 'circulo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'curso',
        'creador_id',
        'max_miembros',
        'codigo_invitacion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'max_miembros' => 'integer',
    ];

    // ---------- Relaciones ----------

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creador_id');
    }

    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'miembro_circulo', 'circulo_id', 'usuario_id')
            ->withPivot('rol', 'estado', 'fecha_union')
            ->using(MiembroCirculo::class);
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeChat::class, 'circulo_id');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'circulo_id');
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(MaterialCompartido::class, 'circulo_id');
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'circulo_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(CalificacionCirculo::class, 'circulo_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'circulo_id');
    }

    // ---------- Helpers ----------

    /** Promedio de calificaciones del círculo */
    public function getPromedioCalificacionAttribute(): float
    {
        return round($this->calificaciones()->avg('puntuacion') ?? 0, 1);
    }
}
