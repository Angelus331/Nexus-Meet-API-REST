<?php

namespace App\Http\Controllers;

use App\Http\Requests\Miembro\ActualizarMiembroRequest;
use App\Http\Requests\Miembro\AgregarMiembroRequest;
use App\Models\Circulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MiembroController extends Controller
{
    /**
     * GET /circulos/{circulo}/miembros
     */
    public function index(Circulo $circulo): JsonResponse
    {
        return response()->json(['data' => $circulo->miembros]);
    }

    /**
     * POST /circulos/{circulo}/miembros
     */
    public function store(AgregarMiembroRequest $request, Circulo $circulo): JsonResponse
    {
        $data = $request->validated();

        abort_if(
            $circulo->miembros()->count() >= $circulo->max_miembros,
            409,
            'Este círculo ya alcanzó su límite de miembros'
        );

        $circulo->miembros()->syncWithoutDetaching([
            $data['usuario_id'] => [
                'rol' => $data['rol'] ?? 'miembro',
                'estado' => 'activo',
            ],
        ]);

        return response()->json(['data' => $circulo->miembros], 201);
    }

    /**
     * PUT /circulos/{circulo}/miembros/{usuario}
     */
    public function update(ActualizarMiembroRequest $request, Circulo $circulo, Usuario $usuario): JsonResponse
    {
        $circulo->miembros()->updateExistingPivot($usuario->id, $request->validated());

        return response()->json(['data' => $circulo->miembros]);
    }

    /**
     * DELETE /circulos/{circulo}/miembros/{usuario}
     */
    public function destroy(Request $request, Circulo $circulo, Usuario $usuario): JsonResponse
    {
        $esAdmin = $circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();
        $esPropio = $usuario->id === $request->user()->id;

        abort_unless($esAdmin || $esPropio, 403, 'No tienes permiso para expulsar a este miembro');

        $circulo->miembros()->detach($usuario->id);

        return response()->json(['message' => 'Miembro removido del círculo']);
    }
}
