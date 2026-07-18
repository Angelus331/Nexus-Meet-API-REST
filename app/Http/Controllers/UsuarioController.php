<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\ActualizarUsuarioRequest;
use App\Http\Requests\Usuario\GuardarFcmTokenRequest;
use App\Http\Requests\Usuario\SubirFotoRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
{
    /**
     * GET /usuarios (solo admin de plataforma)
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->es_admin_plataforma ?? false, 403, 'Solo un administrador puede listar usuarios');

        $query = Usuario::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'ilike', "%{$request->q}%")
                  ->orWhere('correo', 'ilike', "%{$request->q}%");
            });
        }

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    /**
     * GET /usuarios/{usuario}
     */
    public function show(Usuario $usuario): JsonResponse
    {
        return response()->json(['data' => $usuario]);
    }

    /**
     * PUT /usuarios/{usuario}
     */
    public function update(ActualizarUsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $usuario->update($request->validated());

        return response()->json(['data' => $usuario]);
    }

    /**
     * POST /usuarios/{usuario}/foto
     */
    public function subirFoto(SubirFotoRequest $request, Usuario $usuario): JsonResponse
    {
        $ruta = $request->file('foto')->store('perfiles', 'public');

        $usuario->update([
            'foto_perfil_url' => Storage::disk('public')->url($ruta),
        ]);

        return response()->json(['data' => $usuario]);
    }

    /**
     * PUT /usuarios/fcm-token
     * Flutter llama esto después del login para registrar el dispositivo
     * y poder recibir notificaciones push.
     */
    public function guardarFcmToken(GuardarFcmTokenRequest $request): JsonResponse
    {
        $request->user()->update([
            'fcm_token' => $request->validated('fcm_token'),
        ]);

        return response()->json(['message' => 'Token guardado']);
    }

    /**
     * DELETE /usuarios/{usuario} (desactivar cuenta)
     */
    public function destroy(Request $request, Usuario $usuario): JsonResponse
    {
        $esPropio = $usuario->id === $request->user()->id;
        $esAdmin = $request->user()->es_admin_plataforma ?? false;

        abort_unless($esPropio || $esAdmin, 403, 'No tienes permiso para desactivar esta cuenta');

        $usuario->update(['activo' => false]);

        return response()->json(['message' => 'Cuenta desactivada correctamente']);
    }
}