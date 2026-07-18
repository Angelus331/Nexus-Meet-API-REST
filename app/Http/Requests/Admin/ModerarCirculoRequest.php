<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModerarCirculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Requiere la columna usuario.es_admin_plataforma (ver nota al final de la guía)
        return $this->user()->es_admin_plataforma ?? false;
    }

    public function rules(): array
    {
        return [
            'activo' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un administrador de la plataforma puede moderar círculos',
        ];
    }
}