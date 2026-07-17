<?php

namespace App\Http\Requests\Circulo;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarCirculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $circulo = $this->route('circulo');

        return $circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'max_miembros' => 'nullable|integer|min:2|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un admin del círculo puede editarlo',
        ];
    }
}
