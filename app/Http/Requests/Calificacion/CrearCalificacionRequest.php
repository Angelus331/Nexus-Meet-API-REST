<?php

namespace App\Http\Requests\Calificacion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearCalificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $circulo = $this->route('circulo');

        // Debe (haber sido) miembro del círculo para poder calificarlo
        return $circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->exists();
    }

    public function rules(): array
    {
        $circuloId = $this->route('circulo')->id;

        return [
            'puntuacion' => 'required|integer|between:1,5',
            'comentario' => 'nullable|string|max:1000',
            // La BD ya tiene UNIQUE(circulo_id, usuario_id); esto da un mensaje 422 claro en vez de un error 500
            'usuario_id_check' => [
                Rule::unique('calificacion_circulo', 'usuario_id')
                    ->where(fn ($query) => $query->where('circulo_id', $circuloId)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Debes haber sido miembro de este círculo para calificarlo',
            'puntuacion.between' => 'La puntuación debe estar entre 1 y 5',
            'usuario_id_check.unique' => 'Ya calificaste este círculo anteriormente',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Campo "fantasma" solo para poder aplicar la regla unique de forma declarativa
        $this->merge(['usuario_id_check' => $this->user()->id]);
    }
}