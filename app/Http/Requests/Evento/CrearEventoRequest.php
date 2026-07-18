<?php

namespace App\Http\Requests\Evento;

use Illuminate\Foundation\Http\FormRequest;

class CrearEventoRequest extends FormRequest
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
            'tipo' => 'required|in:sesion_estudio,turno_comida',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'required|date|after:now',
            'ubicacion' => 'nullable|string|max:200',
            'responsable_id' => 'nullable|exists:usuario,id',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo un admin del círculo puede crear eventos',
            'fecha_hora.after' => 'La fecha del evento debe ser futura',
        ];
    }
}