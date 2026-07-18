<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mensaje\ActualizarMensajeRequest;
use App\Http\Requests\Mensaje\CrearMensajeRequest;
use App\Http\Requests\Mensaje\ReportarMensajeRequest;
use App\Models\Circulo;
use App\Models\MensajeChat;
use App\Models\ReporteMensaje;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MensajeController extends Controller
{
    /**
     * GET /circulos/{circulo}/mensajes?before=id
     */
    public function index(Request $request, Circulo $circulo): JsonResponse
    {
        $query = $circulo->mensajes()->with('usuario')->orderByDesc('created_at');

        if ($request->filled('before')) {
            $query->where('id', '<', $request->before);
        }

        return response()->json($query->limit($request->get('per_page', 30))->get());
    }

    /**
     * POST /circulos/{circulo}/mensajes
     */
    public function store(CrearMensajeRequest $request, Circulo $circulo): JsonResponse
    {
        $mensaje = $circulo->mensajes()->create([
            'usuario_id' => $request->user()->id,
            'contenido' => $request->validated('contenido'),
            'tipo' => $request->validated('tipo') ?? 'texto',
        ]);

        // Aquí se dispararía el evento de broadcasting: event(new MensajeEnviado($mensaje));

        return response()->json(['data' => $mensaje->load('usuario')], 201);
    }

    /**
     * PUT /mensajes/{mensaje}
     */
    public function update(ActualizarMensajeRequest $request, MensajeChat $mensaje): JsonResponse
    {
        $mensaje->update([
            'contenido' => $request->validated('contenido'),
            'editado' => true,
        ]);

        return response()->json(['data' => $mensaje]);
    }

    /**
     * DELETE /mensajes/{mensaje}
     */
    public function destroy(Request $request, MensajeChat $mensaje): JsonResponse
    {
        $esAutor = $mensaje->usuario_id === $request->user()->id;
        $esAdmin = $mensaje->circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        abort_unless($esAutor || $esAdmin, 403, 'No tienes permiso para eliminar este mensaje');

        $mensaje->delete();

        return response()->json(['message' => 'Mensaje eliminado']);
    }

    /**
     * POST /mensajes/{mensaje}/reportar
     */
    public function reportar(ReportarMensajeRequest $request, MensajeChat $mensaje): JsonResponse
    {
        $reporte = ReporteMensaje::firstOrCreate(
            ['mensaje_id' => $mensaje->id, 'usuario_id' => $request->user()->id],
            ['motivo' => $request->validated('motivo'), 'resuelto' => false]
        );

        return response()->json(['data' => $reporte], 201);
    }
}