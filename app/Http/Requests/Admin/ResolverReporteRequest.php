<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResolverReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->es_admin_plataforma ?? false;
    }

    public function rules(): array
    {
        return [
            'accion' => 'required|in:mantener,eliminar',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un administrador de la plataforma puede resolver reportes',
            'accion.in' => 'La acción debe ser "mantener" o "eliminar"',
        ];
    }
}