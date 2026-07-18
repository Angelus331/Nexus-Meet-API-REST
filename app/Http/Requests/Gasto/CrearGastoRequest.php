<?php

namespace App\Http\Requests\Gasto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CrearGastoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $circulo = $this->route('circulo');

        return $circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->wherePivot('estado', 'activo')
            ->exists();
    }

    public function rules(): array
    {
        return [
            'evento_id' => 'nullable|exists:evento,id',
            'monto_total' => 'required|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:255',
            'miembros_ids' => 'required|array|min:1',
            'miembros_ids.*' => 'exists:usuario,id',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Debes ser miembro activo de este círculo para registrar un gasto',
            'miembros_ids.required' => 'Debes indicar entre quiénes se divide el gasto',
        ];
    }

    /**
     * Validación extra: todos los miembros_ids deben pertenecer al círculo.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $circulo = $this->route('circulo');
            $idsInvalidos = collect($this->input('miembros_ids', []))
                ->reject(fn ($id) => $circulo->miembros()->where('usuario_id', $id)->exists());

            if ($idsInvalidos->isNotEmpty()) {
                $validator->errors()->add(
                    'miembros_ids',
                    'Todos los usuarios deben ser miembros del círculo antes de incluirlos en el gasto'
                );
            }
        });
    }
}