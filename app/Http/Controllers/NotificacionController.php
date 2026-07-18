<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notificacion\MarcarLeidaRequest;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificacionController extends Controller
{
    /**
     * GET /notificaciones
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notificaciones()->latest('created_at');

        if ($request->filled('leida')) {
            $query->where('leida', filter_var($request->leida, FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    /**
     * PUT /notificaciones/{notificacion}/leer
     */
    public function leer(MarcarLeidaRequest $request, Notificacion $notificacion): JsonResponse
    {
        $notificacion->update(['leida' => true]);

        return response()->json(['data' => $notificacion]);
    }

    /**
     * PUT /notificaciones/leer-todas
     */
    public function leerTodas(Request $request): JsonResponse
    {
        $request->user()->notificaciones()->where('leida', false)->update(['leida' => true]);

        return response()->json(['message' => 'Todas las notificaciones fueron marcadas como leídas']);
    }
}