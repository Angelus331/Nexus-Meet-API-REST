<?php

namespace App\Http\Controllers;

use App\Models\Circulo;
use App\Models\MensajeChat;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * GET /admin/metrics
     */
    public function metrics()
    {
        return response()->json([
            'usuarios' => Usuario::count(),
            'circulos' => Circulo::count(),
            'mensajes' => MensajeChat::count(),
        ]);
    }

    /**
     * GET /admin/circulos
     */
    public function circulos(Request $request)
    {
        $query = Circulo::with('creador');

        if ($request->filled('q')) {
            $query->where('nombre', 'like', "%{$request->q}%");
        }

        return response()->json(
            $query->paginate($request->get('per_page',20))
        );
    }

    /**
     * PUT /admin/circulos/{circulo}/moderar
     */
    public function moderarCirculo(Request $request, Circulo $circulo)
    {
        $data = $request->validate([
            'activo' => 'required|boolean',
        ]);

        $circulo->update([
            'activo' => $data['activo']
        ]);

        return response()->json([
            'message' => 'Estado del círculo actualizado.',
            'data' => $circulo
        ]);
    }

    /**
     * GET /admin/usuarios
     */
    public function usuarios(Request $request)
    {
        $query = Usuario::query();

        if ($request->filled('q')) {
            $query->where('nombre','like',"%{$request->q}%")
                  ->orWhere('apellido','like',"%{$request->q}%")
                  ->orWhere('correo','like',"%{$request->q}%");
        }

        return response()->json(
            $query->paginate($request->get('per_page',20))
        );
    }

    /**
     * PUT /admin/usuarios/{usuario}/suspender
     */
    public function suspenderUsuario(Usuario $usuario)
    {
        $usuario->update([
            'activo' => false,
        ]);

        return response()->json([
            'message' => 'Usuario suspendido.',
            'data' => $usuario
        ]);
    }

    /**
     * GET /admin/mensajes/reportados
     */
    public function mensajesReportados()
    {
        // Cambiar cuando exista tabla de reportes
        return response()->json([
            'message' => 'Función pendiente de implementar.',
            'data' => []
        ]);
    }

    /**
     * PUT /admin/mensajes/{mensaje}/resolver
     */
    public function resolverReporte(MensajeChat $mensaje)
    {
        // Cambiar cuando exista tabla de reportes
        return response()->json([
            'message' => 'Reporte resuelto.',
            'data' => $mensaje
        ]);
    }
}
