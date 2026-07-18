<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ModerarCirculoRequest;
use App\Http\Requests\Admin\ResolverReporteRequest;
use App\Http\Requests\Admin\SuspenderUsuarioRequest;
use App\Models\Circulo;
use App\Models\MensajeChat;
use App\Models\ReporteMensaje;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * GET /admin/metrics
     */
    public function metrics(Request $request): JsonResponse
    {
        abort_unless($request->user()->es_admin_plataforma ?? false, 403, 'Solo un administrador puede ver métricas');

        return response()->json(['data' => [
            'circulos_activos' => Circulo::where('activo', true)->count(),
            'circulos_totales' => Circulo::count(),
            'usuarios_totales' => Usuario::count(),
            'usuarios_activos' => Usuario::where('activo', true)->count(),
            'mensajes_reportados_pendientes' => ReporteMensaje::where('resuelto', false)->count(),
        ]]);
    }

    /**
     * GET /admin/circulos
     */
    public function circulos(Request $request): JsonResponse
    {
        abort_unless($request->user()->es_admin_plataforma ?? false, 403, 'Solo un administrador puede ver esta lista');

        $query = Circulo::query()->withCount('miembros');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    /**
     * PUT /admin/circulos/{circulo}/moderar
     */
    public function moderarCirculo(ModerarCirculoRequest $request, Circulo $circulo): JsonResponse
    {
        $circulo->update(['activo' => $request->validated('activo')]);

        return response()->json(['data' => $circulo]);
    }

    /**
     * GET /admin/usuarios
     */
    public function usuarios(Request $request): JsonResponse
    {
        abort_unless($request->user()->es_admin_plataforma ?? false, 403, 'Solo un administrador puede ver esta lista');

        $query = Usuario::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'ilike', "%{$request->q}%")
                  ->orWhere('correo', 'ilike', "%{$request->q}%");
            });
        }
        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    /**
     * PUT /admin/usuarios/{usuario}/suspender
     */
    public function suspenderUsuario(SuspenderUsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $usuario->update(['activo' => $request->validated('activo')]);

        return response()->json(['data' => $usuario]);
    }

    /**
     * GET /admin/mensajes/reportados
     */
    public function mensajesReportados(Request $request): JsonResponse
    {
        abort_unless($request->user()->es_admin_plataforma ?? false, 403, 'Solo un administrador puede ver reportes');

        $reportes = ReporteMensaje::where('resuelto', false)
            ->with(['mensaje.usuario', 'mensaje.circulo', 'usuario'])
            ->latest('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($reportes);
    }

    /**
     * PUT /admin/mensajes/{mensaje}/resolver
     */
    public function resolverReporte(ResolverReporteRequest $request, MensajeChat $mensaje): JsonResponse
    {
        $mensaje->reportes()->update(['resuelto' => true]);

        if ($request->validated('accion') === 'eliminar') {
            $mensaje->delete(); // los reportes de este mensaje se eliminan en cascada
        }

        return response()->json(['message' => 'Reporte resuelto']);
    }
}