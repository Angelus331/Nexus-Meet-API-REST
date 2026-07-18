<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    protected $table = 'usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'password_hash',
        'google_id',
        'foto_perfil_url',
        'facultad',
        'carrera',
        'telefono',
        'email_verificado_en',
        'activo',
        'es_admin_plataforma',
        'fcm_token',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'email_verificado_en' => 'datetime',
        'activo' => 'boolean',
        'es_admin_plataforma' => 'boolean',
    ];

    // Laravel busca por defecto la columna "password"; aquí usamos "password_hash"
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // ---------- JWTSubject ----------
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // ---------- Relaciones ----------

    /** Círculos a los que pertenece (vía tabla pivote miembro_circulo) */
    public function circulos(): BelongsToMany
    {
        return $this->belongsToMany(Circulo::class, 'miembro_circulo', 'usuario_id', 'circulo_id')
            ->withPivot('rol', 'estado', 'fecha_union')
            ->using(MiembroCirculo::class);
    }

    /** Círculos que este usuario creó */
    public function circulosCreados(): HasMany
    {
        return $this->hasMany(Circulo::class, 'creador_id');
    }

    /** Mensajes de chat que envió */
    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeChat::class, 'usuario_id');
    }

    /** Eventos donde es responsable (turnos de comida) */
    public function eventosResponsable(): HasMany
    {
        return $this->hasMany(Evento::class, 'responsable_id');
    }

    /** Materiales que subió */
    public function materiales(): HasMany
    {
        return $this->hasMany(MaterialCompartido::class, 'usuario_id');
    }

    /** Gastos que pagó (registró) */
    public function gastosPagados(): HasMany
    {
        return $this->hasMany(Gasto::class, 'usuario_pagador_id');
    }

    /** Su parte en cada gasto dividido */
    public function gastoDetalles(): HasMany
    {
        return $this->hasMany(GastoDetalle::class, 'usuario_id');
    }

    /** Calificaciones que dejó en círculos */
    public function calificaciones(): HasMany
    {
        return $this->hasMany(CalificacionCirculo::class, 'usuario_id');
    }

    /** Sus notificaciones */
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }
}