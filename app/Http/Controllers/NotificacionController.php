<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * GET /notificaciones
     */
    public function index(Request $request)
    {
        $notificaciones = Notificacion::with('circulo')
            ->where('usuario_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($notificaciones);
    }

    /**
     * PUT /notificaciones/{notificacion}/leer
     */
    public function leer(Request $request, Notificacion $notificacion)
    {
        if ($notificacion->usuario_id != $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta notificación.'
            ], 403);
        }

        $notificacion->update([
            'leida' => true,
        ]);

        return response()->json([
            'message' => 'Notificación marcada como leída.',
            'data' => $notificacion
        ]);
    }

    /**
     * PUT /notificaciones/leer-todas
     */
    public function leerTodas(Request $request)
    {
        Notificacion::where('usuario_id', $request->user()->id)
            ->where('leida', false)
            ->update([
                'leida' => true,
            ]);

        return response()->json([
            'message' => 'Todas las notificaciones fueron marcadas como leídas.'
        ]);
    }
}
