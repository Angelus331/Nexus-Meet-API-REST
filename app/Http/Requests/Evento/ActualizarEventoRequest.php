<?php

namespace App\Http\Requests\Evento;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $evento = $this->route('evento');

        return $evento->circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();
    }

    public function rules(): array
    {
        return [
            'titulo' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'sometimes|date',
            'ubicacion' => 'nullable|string|max:200',
            'responsable_id' => 'nullable|exists:usuario,id',
            'estado' => 'sometimes|in:programado,en_curso,finalizado,cancelado',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un admin del círculo puede editar este evento',
        ];
    }
}