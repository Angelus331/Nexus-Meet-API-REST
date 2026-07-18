<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class SubirMaterialRequest extends FormRequest
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
            'archivo' => 'required|file|max:20480', // 20 MB
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Debes ser miembro activo de este círculo para subir materiales',
            'archivo.max' => 'El archivo no puede superar los 20 MB',
        ];
    }
}