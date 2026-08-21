<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GuardarCorreoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        return [
             'cod_ban' => 'required|string|max:2',
            'cod_deu' => 'required|string|max:11',
            'tipo'    => 'required|string|in:Personal,Secundario,Trabajo,Otro',
            'correo'        => 'required',
            'usuario'  => 'required|string|min:2',
        ];
    }

    public function messages(): array
    {
        return [
             'correo.required'       => 'El correo es obligatorio.',
             'tipo.in'               => 'El tipo de correo seleccionado no es válido.',

        ];
    }
}
