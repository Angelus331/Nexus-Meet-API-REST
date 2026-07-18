<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class GuardarFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // cualquier usuario autenticado guarda el token de SU propio dispositivo
    }

    public function rules(): array
    {
        return [
            'fcm_token' => 'required|string|max:255',
        ];
    }
}