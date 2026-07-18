<?php

use App\Models\Circulo;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canales de Broadcasting
|--------------------------------------------------------------------------
| Cada círculo tiene su propio canal privado "circulo.{id}". Antes de dejar
| que alguien escuche los mensajes de ese canal (evento MensajeEnviado),
| se verifica que sea miembro ACTIVO del círculo — igual que cualquier
| otro endpoint protegido de la API.
*/

Broadcast::channel('circulo.{circuloId}', function ($usuario, $circuloId) {
    return Circulo::findOrFail($circuloId)
        ->miembros()
        ->where('usuario_id', $usuario->id)
        ->wherePivot('estado', 'activo')
        ->exists();
});