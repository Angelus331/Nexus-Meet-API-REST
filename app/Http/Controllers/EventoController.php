<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    // GET /eventos
    public function index(Request $request)
    {
        $query = Evento::with(['circulo', 'responsable']);

        if ($request->filled('circulo_id')) {
            $query->where('circulo_id', $request->circulo_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('q')) {
            $query->where('titulo', 'like', "%{$request->q}%");
        }

        return response()->json(
            $query->orderBy('fecha_hora')
                  ->paginate($request->get('per_page', 20))
        );
    }
    // POST /eventos
    public function store(Request $request)
    {
        $data = $request->validate([
            'circulo_id' => 'required|exists:circulo,id',
            'tipo' => 'required|string|max:50',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'required|date',
            'ubicacion' => 'nullable|string|max:255',
            'responsable_id' => 'nullable|exists:usuario,id',
            'estado' => 'required|string|max:50',
        ]);

        $evento = Evento::create($data);

        return response()->json([
            'message' => 'Evento creado correctamente',
            'data' => $evento
        ], 201);
    }
    // GET /eventos/{id}
    public function show(Evento $evento)
    {
        return response()->json([
            'data' => $evento->load('circulo', 'responsable', 'gastos')
        ]);
    }
    // PUT /eventos/{id}
    public function update(Request $request, Evento $evento)
    {
        $data = $request->validate([
            'tipo' => 'sometimes|string|max:50',
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'sometimes|date',
            'ubicacion' => 'nullable|string|max:255',
            'responsable_id' => 'nullable|exists:usuario,id',
            'estado' => 'sometimes|string|max:50',
        ]);

        $evento->update($data);

        return response()->json([
            'message' => 'Evento actualizado',
            'data' => $evento
        ]);
    }
    // DELETE /eventos/{id}
    public function destroy(Evento $evento)
    {
        $evento->delete();

        return response()->json([
            'message' => 'Evento eliminado correctamente'
        ]);
    }
}
