<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class SubirFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (int) $this->route('usuario')->id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5 MB
        ];
    }
}