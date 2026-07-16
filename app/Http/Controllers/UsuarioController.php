<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UsuarioController extends Controller
{
   public function index(Request $request)
    {
        $query = Usuario::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->q}%")
                  ->orWhere('apellido', 'like', "%{$request->q}%")
                  ->orWhere('correo', 'like', "%{$request->q}%");
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 20))
        );
    }

    // POST /usuarios
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,correo',
            'password' => 'required|string|min:8',
            'facultad' => 'nullable|string|max:100',
            'carrera' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'foto_perfil_url' => 'nullable|string',
        ]);

        $usuario = Usuario::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'correo' => $data['correo'],
            'password_hash' => Hash::make($data['password']),
            'facultad' => $data['facultad'] ?? null,
            'carrera' => $data['carrera'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'foto_perfil_url' => $data['foto_perfil_url'] ?? null,
            'activo' => true,
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    // GET /usuarios/{id}
    public function show(Usuario $usuario)
    {
        return response()->json([
            'data' => $usuario->load('circulos', 'circulosCreados')
        ]);
    }

    // PUT /usuarios/{id}
    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'correo' => 'sometimes|email|unique:usuario,correo,' . $usuario->id,
            'password' => 'nullable|string|min:8',
            'facultad' => 'nullable|string|max:100',
            'carrera' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'foto_perfil_url' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            unset($data['password']);
        }

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuario actualizado',
            'data' => $usuario
        ]);
    }

    // DELETE /usuarios/{id}
    public function destroy(Usuario $usuario)
    {
        $usuario->update([
            'activo' => false
        ]);

        return response()->json([
            'message' => 'Usuario desactivado'
        ]);
    }
}
