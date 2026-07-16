<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\GastoDetalle;
use App\Models\Usuario;
use App\Models\Circulo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class GastoController extends Controller
{
    /**
     * GET /circulos/{circulo}/gastos
     */
    public function index(Request $request, Circulo $circulo)
    {
        $gastos = Gasto::with([
            'pagador',
            'evento',
            'detalles.usuario'
        ])
            ->where('circulo_id', $circulo->id)
            ->orderByDesc('fecha')
            ->paginate($request->get('per_page', 20));

        return response()->json($gastos);
    }

    /**
     * POST /circulos/{circulo}/gastos
     */
    public function store(Request $request, Circulo $circulo)
    {
        $data = $request->validate([
            'evento_id' => 'nullable|exists:evento,id',
            'monto_total' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'required|date',

            'miembros_ids' => 'required|array|min:1',
            'miembros_ids.*' => 'exists:usuario,id',
        ]);

        return DB::transaction(function () use ($data, $circulo, $request) {

            $gasto = Gasto::create([
                'circulo_id' => $circulo->id,
                'evento_id' => $data['evento_id'] ?? null,
                'usuario_pagador_id' => $request->user()->id,
                'monto_total' => $data['monto_total'],
                'descripcion' => $data['descripcion'] ?? null,
                'fecha' => $data['fecha'],
            ]);

            $cantidad = count($data['miembros_ids']);
            $montoPorPersona = round($data['monto_total'] / $cantidad, 2);

            foreach ($data['miembros_ids'] as $usuarioId) {

                GastoDetalle::create([
                    'gasto_id' => $gasto->id,
                    'usuario_id' => $usuarioId,
                    'monto_asignado' => $montoPorPersona,
                    'pagado' => $usuarioId == $request->user()->id,
                    'fecha_pago' => $usuarioId == $request->user()->id
                        ? now()
                        : null,
                ]);
            }

            return response()->json([
                'message' => 'Gasto registrado correctamente.',
                'data' => $gasto->load([
                    'pagador',
                    'evento',
                    'detalles.usuario'
                ])
            ], 201);
        });
    }

    /**
     * GET /gastos/{gasto}
     */
    public function show(Gasto $gasto)
    {
        return response()->json([
            'data' => $gasto->load([
                'circulo',
                'evento',
                'pagador',
                'detalles.usuario'
            ])
        ]);
    }

    /**
     * PUT /gastos/{gasto}
     */
    public function update(Request $request, Gasto $gasto)
    {
        $data = $request->validate([
            'evento_id' => 'nullable|exists:evento,id',
            'monto_total' => 'sometimes|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'sometimes|date',
        ]);

        $gasto->update($data);

        return response()->json([
            'message' => 'Gasto actualizado correctamente.',
            'data' => $gasto->fresh()
        ]);
    }

    /**
     * DELETE /gastos/{gasto}
     */
    public function destroy(Gasto $gasto)
    {
        $gasto->delete();

        return response()->json([
            'message' => 'Gasto eliminado correctamente.'
        ]);
    }

    /**
     * PUT /gastos/{gasto}/detalle/{usuario}/pagar
     */
    public function marcarPagado(Gasto $gasto, Usuario $usuario)
    {
        $detalle = $gasto->detalles()
            ->where('usuario_id', $usuario->id)
            ->firstOrFail();

        if (!$detalle->pagado) {
            $detalle->update([
                'pagado' => true,
                'fecha_pago' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Pago registrado correctamente.',
            'data' => $detalle
        ]);
    }
}
