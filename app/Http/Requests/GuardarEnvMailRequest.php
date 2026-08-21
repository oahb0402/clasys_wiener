<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarEnvMailRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_ban'        => 'required|string|max:2',
            'cod_deu'        => 'required|string|max:11',
            'fec_pago'       => 'required|date',
            'porcentaje'     => 'required|string',
            'monto_campania' => 'required|numeric|min:0',
            'usuario'        => 'required|string|min:2',
        ];
    }

    public function messages(): array
    {
        return [
            'monto_campania.required'   => 'El monto es obligatorio.',
            'fec_pago.required'         => 'La fecha de pago es obligatoria.',
            'porcentaje.required'         => 'El porcentaje pago es obligatorio.',
        ];
    }
}
