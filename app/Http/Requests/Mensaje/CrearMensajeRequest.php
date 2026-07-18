<?php

namespace App\Http\Requests\Mensaje;

use Illuminate\Foundation\Http\FormRequest;

class CrearMensajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $circulo = $this->route('circulo');

        // Debe ser miembro activo del círculo para poder escribir
        return $circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->wherePivot('estado', 'activo')
            ->exists();
    }

    public function rules(): array
    {
        return [
            'contenido' => 'required|string|max:5000',
            'tipo' => 'nullable|in:texto,imagen,archivo,sistema',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Debes ser miembro activo de este círculo para enviar mensajes',
        ];
    }
}