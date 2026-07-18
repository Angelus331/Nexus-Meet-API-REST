<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo el propio usuario puede editar su perfil (el {usuario} viene del route model binding)
        return (int) $this->route('usuario')->id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'facultad' => 'nullable|string|max:100',
            'carrera' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo puedes editar tu propio perfil',
        ];
    }
}