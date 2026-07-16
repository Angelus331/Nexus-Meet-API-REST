<?php

namespace App\Http\Controllers;

use App\Models\MaterialCompartido;
use App\Models\Circulo;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // GET /circulos/{circulo}/materiales
    public function index(Request $request, Circulo $circulo)
    {
        $query = MaterialCompartido::with(['usuario', 'circulo'])
            ->where('circulo_id', $circulo->id);

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('tipo_archivo')) {
            $query->where('tipo_archivo', $request->tipo_archivo);
        }

        if ($request->filled('q')) {
            $query->where('nombre_archivo', 'like', "%{$request->q}%");
        }

        return response()->json(
            $query->orderByDesc('created_at')
                  ->paginate($request->get('per_page', 20))
        );
    }

    // POST /circulos/{circulo}/materiales
    public function store(Request $request, Circulo $circulo)
    {
        $data = $request->validate([
            'nombre_archivo' => 'required|string|max:255',
            'url_archivo' => 'required|string|max:500',
            'tipo_archivo' => 'required|string|max:50',
            'tamano_bytes' => 'required|integer|min:0',
        ]);

        $data['circulo_id'] = $circulo->id;
        $data['usuario_id'] = $request->user()->id;

        $material = MaterialCompartido::create($data);

        return response()->json([
            'message' => 'Material compartido correctamente.',
            'data' => $material
        ], 201);
    }

    // DELETE /materiales/{material}
    public function destroy(Request $request, MaterialCompartido $material)
    {
        if ($material->usuario_id != $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este material.'
            ], 403);
        }

        $material->delete();

        return response()->json([
            'message' => 'Material eliminado correctamente.'
        ]);
    }
}