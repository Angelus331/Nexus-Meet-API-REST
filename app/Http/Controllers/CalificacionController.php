<?php

namespace App\Http\Controllers;

use App\Models\CalificacionCirculo;
use App\Models\Circulo;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    // GET /circulos/{circulo}/calificaciones
    public function index(Request $request, Circulo $circulo)
    {
        $calificaciones = CalificacionCirculo::with('usuario')
            ->where('circulo_id', $circulo->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($calificaciones);
    }

    // POST /circulos/{circulo}/calificaciones
    public function store(Request $request, Circulo $circulo)
    {
        $data = $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
        ]);

        $calificacion = CalificacionCirculo::create([
            'circulo_id' => $circulo->id,
            'usuario_id' => $request->user()->id,
            'puntuacion' => $data['puntuacion'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return response()->json([
            'message' => 'Calificación registrada correctamente.',
            'data' => $calificacion->load('usuario')
        ], 201);
    }

}
