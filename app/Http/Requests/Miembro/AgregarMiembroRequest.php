<?php

namespace App\Http\Requests\Miembro;

use Illuminate\Foundation\Http\FormRequest;

class AgregarMiembroRequest extends FormRequest
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
            'usuario_id' => 'required|exists:usuario,id',
            'rol' => 'nullable|in:admin,miembro',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un admin del círculo puede agregar miembros',
        ];
    }
}
