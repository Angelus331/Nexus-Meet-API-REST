<?php

namespace App\Http\Requests\Circulo;

use Illuminate\Foundation\Http\FormRequest;

class UnirseCirculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_invitacion' => 'required|string|exists:circulo,codigo_invitacion',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_invitacion.exists' => 'El código de invitación no es válido',
        ];
    }
}
