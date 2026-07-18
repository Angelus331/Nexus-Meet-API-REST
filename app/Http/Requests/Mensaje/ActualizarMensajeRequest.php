<?php

namespace App\Http\Requests\Mensaje;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarMensajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $mensaje = $this->route('mensaje');

        return $mensaje->usuario_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'contenido' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo puedes editar tus propios mensajes',
        ];
    }
}