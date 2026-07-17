<?php

namespace App\Http\Controllers;

use App\Models\MensajeChat;
use App\Models\Circulo; //agregue cirulo model
use Illuminate\Http\Request;


class MensajeController extends Controller
{
    // GET /mensajes
    public function index(Request $request)
    {
        $query = MensajeChat::with(['usuario', 'circulo']);

        if ($request->filled('circulo_id')) {
            $query->where('circulo_id', $request->circulo_id);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return response()->json(
            $query->orderBy('created_at', 'asc')
                ->paginate($request->get('per_page', 20))
        );
    }

    // POST /mensajes
        public function store(Request $request, Circulo $circulo)
    {
        $data = $request->validate([
            'contenido' => 'required|string',
            'tipo' => 'required|in:texto,imagen,archivo',
        ]);

        $mensaje = MensajeChat::create([
            'circulo_id' => $circulo->id,
            'usuario_id' => $request->user()->id,//modfique que no encontrba id
            'contenido' => $data['contenido'],
            'tipo' => $data['tipo'] ?? 'texto',
            'editado' => false,
        ]);

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
            'data' => $mensaje->load('usuario')
        ], 201);
    }


    // PUT /mensajes/{id}
    public function update(Request $request, MensajeChat $mensaje)
    {
        if ($mensaje->usuario_id != auth()->id) {
            return response()->json([
                'message' => 'No puedes editar este mensaje.'
            ], 403);
        }

        $data = $request->validate([
            'contenido' => 'required|string'
        ]);

        $mensaje->update([
            'contenido' => $data['contenido'],
            'editado' => true,
        ]);

        return response()->json([
            'message' => 'Mensaje actualizado',
            'data' => $mensaje
        ]);
    }

    // DELETE /mensajes/{id}
    public function destroy(MensajeChat $mensaje)
    {
        if ($mensaje->usuario_id != auth()->id) {
            return response()->json([
                'message' => 'No puedes eliminar este mensaje.'
            ], 403);
        }

        $mensaje->delete();

        return response()->json([
            'message' => 'Mensaje eliminado'
        ]);
    }

    public function reportar(MensajeChat $mensaje)
    {
        $mensaje->update([
            'reportado' => true,
        ]);

        return response()->json([
            'message' => 'Mensaje reportado correctamente.',
            'data' => $mensaje,
        ]);
    }
}
