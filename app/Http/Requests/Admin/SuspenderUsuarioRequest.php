<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SuspenderUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
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
            'authorize' => 'Solo un administrador de la plataforma puede suspender usuarios',
        ];
    }
}