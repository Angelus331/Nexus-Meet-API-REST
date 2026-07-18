<?php

namespace App\Http\Requests\Gasto;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarGastoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $gasto = $this->route('gasto');

        $esPagador = $gasto->usuario_pagador_id === $this->user()->id;
        $esAdmin = $gasto->circulo->miembros()
            ->where('usuario_id', $this->user()->id)
            ->wherePivot('rol', 'admin')
            ->exists();

        return $esPagador || $esAdmin;
    }

    public function rules(): array
    {
        return [
            'monto_total' => 'sometimes|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Solo quien pagó el gasto o un admin del círculo puede editarlo',
        ];
    }
}