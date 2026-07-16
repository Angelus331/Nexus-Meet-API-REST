<?php

namespace App\Http\Controllers;

use App\Models\Circulo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class CirculoController extends Controller
{
    // GET /circulosuse Illuminate\Support\Str;
    public function index(Request $request)
    {
        $query = Circulo::query()->where('activo', true);

        if ($request->filled('tipo')) $query->where('tipo', $request->tipo);
        if ($request->filled('curso')) $query->where('curso', 'like', "%{$request->curso}%");
        // if ($request->filled('curso')) $query->where('curso', 'ilike', "%{$request->curso}%");postgrel y el otro mysql
        if ($request->filled('q')) $query->where('nombre', 'like', "%{$request->q}%");
        // if ($request->filled('q')) $query->where('nombre', 'ilike', "%{$request->q}%");

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    // POST /circulos
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:estudio,comida',
            'curso' => 'nullable|string|max:100',
            'max_miembros' => 'nullable|integer|min:2',
        ]);

        $data['creador_id'] = $request->user()->id;
        $data['codigo_invitacion'] = strtoupper(Str::random(8));

        $circulo = Circulo::create($data);
        $circulo->miembros()->attach($request->user()->id, ['rol' => 'admin', 'estado' => 'activo']);

        return response()->json(['data' => $circulo], 201);
    }

    // GET /circulos/{id}
    public function show(Circulo $circulo)
    {
        return response()->json(['data' => $circulo->load('creador', 'miembros')]);
    }

    // PUT /circulos/{id}
    public function update(Request $request, Circulo $circulo)
    {
        $this->autorizarAdmin($request, $circulo);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'max_miembros' => 'nullable|integer|min:2',
        ]);

        $circulo->update($data);
        return response()->json(['data' => $circulo]);
    }

    // DELETE /circulos/{id}
    public function destroy(Request $request, Circulo $circulo)
    {
        $this->autorizarAdmin($request, $circulo);
        $circulo->update(['activo' => false]); // borrado lógico
        return response()->json(['message' => 'Círculo eliminado']);
    }

    // POST /circulos/join
    public function join(Request $request)
    {
        $data = $request->validate(['codigo_invitacion' => 'required|string']);
        $circulo = Circulo::where('codigo_invitacion', $data['codigo_invitacion'])->firstOrFail();

        $circulo->miembros()->syncWithoutDetaching([
            $request->user()->id => ['rol' => 'miembro', 'estado' => 'activo']
        ]);

        return response()->json(['data' => $circulo]);
    }

    // POST /circulos/{id}/leave
    public function leave(Request $request, Circulo $circulo)
    {
        $circulo->miembros()->detach($request->user()->id);
        return response()->json(['message' => 'Saliste del círculo']);
    }

    private function autorizarAdmin(Request $request, Circulo $circulo)
    {
        $esAdmin = $circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')->exists();

        abort_if(!$esAdmin, 403, 'Solo un admin del círculo puede hacer esto');
    }
}
