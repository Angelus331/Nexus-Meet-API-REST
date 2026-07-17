<?php

namespace App\Http\Requests\Circulo;

use Illuminate\Foundation\Http\FormRequest;

class CrearCirculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // cualquier usuario autenticado puede crear un círculo
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:estudio,comida',
            'curso' => 'nullable|string|max:100',
            'max_miembros' => 'nullable|integer|min:2|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.in' => 'El tipo debe ser "estudio" o "comida"',
        ];
    }
}
