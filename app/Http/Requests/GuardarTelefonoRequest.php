<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarTelefonoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_ban' => 'required|string|max:2',
            'cod_deu' => 'required|string|max:11',
            'tipo'    => 'required|string|in:Celular,Oficina,Casa,Otro',
            'numero'  => 'required|digits_between:7,15',
            'usuario' => 'required|string|min:2',
        ];
    }

    public function messages(): array
    {
        return [
            'numero.required'       => 'El número de teléfono es obligatorio.',
            'numero.digits_between' => 'El teléfono debe contener solo números (entre 7 y 15 dígitos).',
            'tipo.in'               => 'El tipo de teléfono seleccionado no es válido.',
        ];
    }
}
