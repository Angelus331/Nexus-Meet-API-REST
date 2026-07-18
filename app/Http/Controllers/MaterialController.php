<?php

namespace App\Http\Controllers;

use App\Http\Requests\Material\SubirMaterialRequest;
use App\Models\Circulo;
use App\Models\MaterialCompartido;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * GET /circulos/{circulo}/materiales
     */
    public function index(Circulo $circulo): JsonResponse
    {
        return response()->json([
            'data' => $circulo->materiales()->with('usuario')->latest('created_at')->get(),
        ]);
    }

    /**
     * POST /circulos/{circulo}/materiales
     */
    public function store(SubirMaterialRequest $request, Circulo $circulo): JsonResponse
    {
        $archivo = $request->file('archivo');
        $ruta = $archivo->store('materiales/circulo_' . $circulo->id, 'public');

        $material = $circulo->materiales()->create([
            'usuario_id' => $request->user()->id,
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'url_archivo' => Storage::disk('public')->url($ruta),
            'tipo_archivo' => $archivo->getClientOriginalExtension(),
            'tamano_bytes' => $archivo->getSize(),
        ]);

        return response()->json(['data' => $material], 201);
    }

    /**
     * DELETE /materiales/{material}
     */
    public function destroy(Request $request, MaterialCompartido $material): JsonResponse
    {
        $esAutor = $material->usuario_id === $request->user()->id;
        $esAdmin = $material->circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        abort_unless($esAutor || $esAdmin, 403, 'No tienes permiso para eliminar este material');

        $material->delete();

        return response()->json(['message' => 'Material eliminado']);
    }
}