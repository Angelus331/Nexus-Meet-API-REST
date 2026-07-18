<?php

namespace App\Http\Controllers;

use App\Http\Requests\Evento\ActualizarEventoRequest;
use App\Http\Requests\Evento\CrearEventoRequest;
use App\Models\Circulo;
use App\Models\Evento;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventoController extends Controller
{
    /**
     * GET /circulos/{circulo}/eventos
     */
    public function index(Circulo $circulo): JsonResponse
    {
        return response()->json([
            'data' => $circulo->eventos()->orderBy('fecha_hora')->get(),
        ]);
    }

    /**
     * POST /circulos/{circulo}/eventos
     */
    public function store(CrearEventoRequest $request, Circulo $circulo, NotificacionService $notificaciones): JsonResponse
    {
        $evento = $circulo->eventos()->create($request->validated());

        if ($evento->responsable_id) {
            $notificaciones->enviar(
                usuario: $evento->responsable,
                tipo: 'turno_asignado',
                titulo: 'Te asignaron un turno',
                contenido: "Turno de {$circulo->nombre}: {$evento->titulo}",
                circulo: $circulo,
            );
        }

        return response()->json(['data' => $evento], 201);
    }

    /**
     * GET /eventos/{evento}
     */
    public function show(Evento $evento): JsonResponse
    {
        return response()->json(['data' => $evento->load('circulo', 'responsable')]);
    }

    /**
     * PUT /eventos/{evento}
     */
    public function update(ActualizarEventoRequest $request, Evento $evento): JsonResponse
    {
        $evento->update($request->validated());

        return response()->json(['data' => $evento]);
    }

    /**
     * DELETE /eventos/{evento}
     */
    public function destroy(Request $request, Evento $evento): JsonResponse
    {
        $esAdmin = $evento->circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        abort_unless($esAdmin, 403, 'Solo un admin del círculo puede cancelar este evento');

        $evento->update(['estado' => 'cancelado']);

        return response()->json(['message' => 'Evento cancelado']);
    }
}