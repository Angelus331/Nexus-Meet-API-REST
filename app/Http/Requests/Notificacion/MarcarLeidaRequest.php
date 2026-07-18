<?php

namespace App\Http\Requests\Notificacion;

use Illuminate\Foundation\Http\FormRequest;

class MarcarLeidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $notificacion = $this->route('notificacion');

        return $notificacion->usuario_id === $this->user()->id;
    }

    public function rules(): array
    {
        return []; // no recibe cuerpo, solo valida que sea la notificación del usuario
    }

    public function messages(): array
    {
        return [
            'authorize' => 'Esta notificación no te pertenece',
        ];
    }
}