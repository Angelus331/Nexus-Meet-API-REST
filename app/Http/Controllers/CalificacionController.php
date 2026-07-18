<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calificacion\CrearCalificacionRequest;
use App\Models\Circulo;
use Illuminate\Http\JsonResponse;

class CalificacionController extends Controller
{
    /**
     * GET /circulos/{circulo}/calificaciones
     */
    public function index(Circulo $circulo): JsonResponse
    {
        return response()->json([
            'data' => $circulo->calificaciones()->with('usuario')->latest('created_at')->get(),
            'promedio' => $circulo->promedio_calificacion,
        ]);
    }

    /**
     * POST /circulos/{circulo}/calificaciones
     */
    public function store(CrearCalificacionRequest $request, Circulo $circulo): JsonResponse
    {
        $calificacion = $circulo->calificaciones()->create([
            'usuario_id' => $request->user()->id,
            'puntuacion' => $request->validated('puntuacion'),
            'comentario' => $request->validated('comentario'),
        ]);

        return response()->json(['data' => $calificacion], 201);
    }
}