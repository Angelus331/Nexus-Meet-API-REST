<?php

namespace App\Http\Requests\Miembro;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarMiembroRequest extends FormRequest
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
            'rol' => 'sometimes|in:admin,miembro',
            'estado' => 'sometimes|in:activo,inactivo,expulsado',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un admin del círculo puede cambiar el rol o estado de un miembro',
        ];
    }
}
