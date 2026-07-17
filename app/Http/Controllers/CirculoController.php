<?php

namespace App\Http\Controllers;

use App\Http\Requests\Circulo\ActualizarCirculoRequest;
use App\Http\Requests\Circulo\CrearCirculoRequest;
use App\Http\Requests\Circulo\UnirseCirculoRequest;
use App\Models\Circulo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CirculoController extends Controller
{
    /**
     * GET /circulos
     */
    public function index(Request $request): JsonResponse
    {
        $query = Circulo::query()->where('activo', true);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('curso')) {
            $query->where('curso', 'ilike', "%{$request->curso}%");
        }
        if ($request->filled('q')) {
            $query->where('nombre', 'ilike', "%{$request->q}%");
        }

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    /**
     * POST /circulos
     */
    public function store(CrearCirculoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['creador_id'] = $request->user()->id;
        $data['codigo_invitacion'] = strtoupper(Str::random(8));

        $circulo = Circulo::create($data);

        // El creador entra automáticamente como admin del círculo
        $circulo->miembros()->attach($request->user()->id, [
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        return response()->json(['data' => $circulo->load('miembros')], 201);
    }

    /**
     * GET /circulos/{circulo}
     */
    public function show(Circulo $circulo): JsonResponse
    {
        return response()->json(['data' => $circulo->load('creador', 'miembros')]);
    }

    /**
     * PUT /circulos/{circulo}
     */
    public function update(ActualizarCirculoRequest $request, Circulo $circulo): JsonResponse
    {
        $circulo->update($request->validated());

        return response()->json(['data' => $circulo]);
    }

    /**
     * DELETE /circulos/{circulo}
     */
    public function destroy(Request $request, Circulo $circulo): JsonResponse
    {
        $this->autorizarAdmin($request, $circulo);

        $circulo->update(['activo' => false]); // borrado lógico

        return response()->json(['message' => 'Círculo eliminado']);
    }

    /**
     * POST /circulos/join
     */
    public function join(UnirseCirculoRequest $request): JsonResponse
    {
        $circulo = Circulo::where('codigo_invitacion', $request->validated('codigo_invitacion'))->firstOrFail();

        abort_if(
            $circulo->miembros()->count() >= $circulo->max_miembros,
            409,
            'Este círculo ya alcanzó su límite de miembros'
        );

        $circulo->miembros()->syncWithoutDetaching([
            $request->user()->id => ['rol' => 'miembro', 'estado' => 'activo'],
        ]);

        return response()->json(['data' => $circulo->load('miembros')]);
    }

    /**
     * POST /circulos/{circulo}/leave
     */
    public function leave(Request $request, Circulo $circulo): JsonResponse
    {
        $circulo->miembros()->detach($request->user()->id);

        return response()->json(['message' => 'Saliste del círculo']);
    }

    private function autorizarAdmin(Request $request, Circulo $circulo): void
    {
        $esAdmin = $circulo->miembros()
            ->where('usuario_id', $request->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        abort_unless($esAdmin, 403, 'Solo un admin del círculo puede hacer esto');
    }
}
