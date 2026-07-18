<?php

namespace App\Http\Requests\Mensaje;

use Illuminate\Foundation\Http\FormRequest;

class ReportarMensajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $mensaje = $this->route('mensaje');

        // Debe ser miembro del círculo donde está el mensaje para poder reportarlo
        return $mensaje->circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'motivo' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Debes ser miembro de este círculo para reportar un mensaje',
        ];
    }
}