<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gasto\ActualizarGastoRequest;
use App\Http\Requests\Gasto\CrearGastoRequest;
use App\Models\Circulo;
use App\Models\Gasto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
{
    /**
     * GET /circulos/{circulo}/gastos
     */
    public function index(Circulo $circulo): JsonResponse
    {
        return response()->json([
            'data' => $circulo->gastos()->with('pagador', 'detalles')->latest('created_at')->get(),
        ]);
    }

    /**
     * POST /circulos/{circulo}/gastos
     * Crea el gasto y lo divide en partes iguales entre los miembros indicados.
     */
    public function store(CrearGastoRequest $request, Circulo $circulo): JsonResponse
    {
        $data = $request->validated();

        $gasto = DB::transaction(function () use ($data, $circulo, $request) {
            $gasto = $circulo->gastos()->create([
                'evento_id' => $data['evento_id'] ?? null,
                'usuario_pagador_id' => $request->user()->id,
                'monto_total' => $data['monto_total'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            $numMiembros = count($data['miembros_ids']);
            $montoPorPersona = round($data['monto_total'] / $numMiembros, 2);

            foreach ($data['miembros_ids'] as $usuarioId) {
                $gasto->detalles()->create([
                    'usuario_id' => $usuarioId,
                    'monto_asignado' => $montoPorPersona,
                    // Quien pagó ya cubrió su propia parte
                    'pagado' => (int) $usuarioId === (int) $request->user()->id,
                    'fecha_pago' => (int) $usuarioId === (int) $request->user()->id ? now() : null,
                ]);
            }

            return $gasto;
        });

        return response()->json(['data' => $gasto->load('detalles.usuario')], 201);
    }

    /**
     * GET /gastos/{gasto}
     */
    public function show(Gasto $gasto): JsonResponse
    {
        return response()->json(['data' => $gasto->load('pagador', 'detalles.usuario', 'evento')]);
    }

    /**
     * PUT /gastos/{gasto}
     */
    public function update(ActualizarGastoRequest $request, Gasto $gasto): JsonResponse
    {
        $gasto->update($request->validated());

        return response()->json(['data' => $gasto]);
    }

    /**
     * DELETE /gastos/{gasto}
     */
    public function destroy(Request $request, Gasto $gasto): JsonResponse
    {
        $esPagador = $gasto->usuario_pagador_id === $request->user()->id;
        $esAdmin = $gasto->circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        abort_unless($esPagador || $esAdmin, 403, 'No tienes permiso para eliminar este gasto');

        $gasto->delete(); // gasto_detalle se elimina en cascada

        return response()->json(['message' => 'Gasto eliminado']);
    }

    /**
     * PUT /gastos/{gasto}/detalle/{usuario}/pagar
     */
    public function marcarPagado(Request $request, Gasto $gasto, Usuario $usuario): JsonResponse
    {
        $esElMismo = $usuario->id === $request->user()->id;
        $esPagador = $gasto->usuario_pagador_id === $request->user()->id;
        $esAdmin = $gasto->circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        abort_unless($esElMismo || $esPagador || $esAdmin, 403, 'No tienes permiso para marcar este pago');

        $detalle = $gasto->detalles()->where('usuario_id', $usuario->id)->firstOrFail();
        $detalle->update(['pagado' => true, 'fecha_pago' => now()]);

        return response()->json(['data' => $detalle]);
    }
}