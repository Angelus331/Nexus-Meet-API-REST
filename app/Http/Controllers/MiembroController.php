<?php

namespace App\Http\Controllers;

use App\Models\MiembroCirculo;
use Illuminate\Http\Request;

class MiembroController extends Controller
{
     // GET /miembros
    public function index(Request $request)
    {
        $query = MiembroCirculo::with(['usuario', 'circulo']);

        if ($request->filled('circulo_id')) {
            $query->where('circulo_id', $request->circulo_id);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 20))
        );
    }

    // POST /miembros
    public function store(Request $request)
    {
        $data = $request->validate([
            'circulo_id' => 'required|exists:circulo,id',
            'usuario_id' => 'required|exists:usuario,id',
            'rol' => 'required|in:admin,miembro',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $data['fecha_union'] = now();

        $miembro = MiembroCirculo::create($data);

        return response()->json([
            'message' => 'Miembro agregado correctamente',
            'data' => $miembro
        ], 201);
    }

    // PUT /miembros/{id}
    public function update(Request $request, MiembroCirculo $miembro)
    {
        $data = $request->validate([
            'rol' => 'sometimes|in:admin,miembro',
            'estado' => 'sometimes|in:activo,inactivo',
        ]);

        $miembro->update($data);

        return response()->json([
            'message' => 'Miembro actualizado',
            'data' => $miembro
        ]);
    }

    // DELETE /miembros/{id}
    public function destroy(MiembroCirculo $miembro)
    {
        $miembro->delete();

        return response()->json([
            'message' => 'Miembro eliminado del círculo'
        ]);
    }
}

