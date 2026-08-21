<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\CatalogService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuardarGestionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación básicas.
     */
    public function rules(): array
    {
        return [
            'tipcon'                   => 'required|string',
            'control'                  => 'required|string',
            'usuario'                  => 'required|string',
            'telef_ges'                => 'nullable|string|max:15',
            'fec_agenda'               => 'required_if:agendar,1|nullable|date',
            'hor_agenda'               => 'required_if:agendar,1|nullable|date_format:H:i',
            'comprobante_confirmacion' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ];
    }

    /**
     * Lógica de validación personalizada tras pasar las reglas básicas.
     */
    public function withValidator($validator)
    {
        // Inyectamos CatalogService desde el contenedor de dependencias
        $catalogService = app(CatalogService::class);

        $validator->after(function ($validator) use ($catalogService) {
            $codigoControl = trim($this->input('control', ''));
            $fechaPromesa  = $this->input('fecha_promesa');

            $hoy    = now()->toDateString();
            $manana = now()->addDay()->toDateString();

            // Obtenemos códigos dinámicos utilizando el servicio con caché
            $codigosPromesa      = $catalogService->obtenerCodigosPromesa();
            $codigosConfirmacion = $catalogService->obtenerCodigosConfirmacion();

            // 1. Validar rango para Secciones de Promesa (Hoy y Mañana)
            if (in_array($codigoControl, $codigosPromesa, true) && $fechaPromesa) {
                if ($fechaPromesa < $hoy || $fechaPromesa > $manana) {
                    $validator->errors()->add('fecha_promesa', 'La fecha de la promesa solo puede ser hoy o mañana.');
                }
            }

            // 2. Validar rango para Secciones de Confirmación (Desde el 1 del mes hasta hoy)
            if (in_array($codigoControl, $codigosConfirmacion, true) && $fechaPromesa) {
                $inicioMes = now()->startOfMonth()->toDateString();

                if ($fechaPromesa < $inicioMes || $fechaPromesa > $hoy) {
                    $validator->errors()->add('fecha_promesa', 'La fecha de confirmación debe pertenecer al mes en curso hasta el día de hoy.');
                }
            }

            // 3. Validar si ya existe una Promesa activa para el cliente
            if (in_array($codigoControl, $codigosPromesa, true)) {
                $clienteId = $this->route('id');
                $codDeu    = $this->input('cod_deu');

                if (!$codDeu && $clienteId) {
                    $codDeu = DB::table('g110')->where('cod_deu', $clienteId)->value('cod_deu');
                }

                if ($codDeu) {
                    $tienePromesaActiva = DB::table('g220')
                        ->where('cod_deu', $codDeu)
                        ->where('tip_rb', $codigoControl)
                        ->where('fec_reg', '>=', now()->format('Y-m-d'))
                        ->exists();

                    if ($tienePromesaActiva) {
                        $validator->errors()->add(
                            'control',
                            "El cliente ya cuenta con una promesa activa bajo el código {$codigoControl}. No es posible repetir esta gestión."
                        );
                    }
                }
            }
        });
    }

    /**
     * Mapea y prepara los datos para su inserción/actualización directa en la tabla g220.
     */
    public function toDatabaseArray(): array
    {
        $now = Carbon::now();
        $fechaActual  = $now->format('Y-m-d');
        $horaActual   = $now->format('H:i:s');
        $horaApertura = $this->input('hora_apertura');

        $datos = [
            'tip_con'     => $this->input('tipcon'),
            'tip_gb'      => $this->input('tipgb'),
            'tip_rb'      => $this->input('control'),
            'sub_res'     => $this->input('subres'),
            'comentario'  => $this->input('comentario'),
            'cond_gral'   => $this->input('condicion'),
            'mon_pro'     => $this->input('monto_promesa') ?: 0,
            'moneda'      => $this->input('moneda_promesa') ?: '',
            'fec_reg'     => $this->input('fecha_promesa') ?: null,
            'control3'    => $this->input('nombre_titular') ?: '',
            'control4'    => $this->input('dni_titular') ?: '',
            'control5'    => $this->input('datos_tarjeta') ?: '',
            'condicion'   => $this->input('medio_pago') ?: '',
            'comenta2'    => $this->input('comenta2') ?: '',
            'uid'         => substr($this->input('comenta2') ?: '', 0, 20),
            'anexo'       => $this->input('anexo') ?: '',
            'con_cam'     => $this->input('con_cam') ?: '',
            'fec_con'     => $fechaActual,
            'fec_sin'     => $fechaActual,
            'control1'    => $horaApertura,
            'control2'    => $horaActual,
            'usuario'     => $this->input('usuario'),
            'telef_ges'   => $this->input('telef_ges'),
            'opcion'      => 'U',
            'corta'       => $this->input('control_grupo'),
            'fec_ges_ini' => "{$fechaActual} {$horaApertura}",
            'fec_ges_fin' => "{$fechaActual} {$horaActual}",
            'horainia'    => $horaApertura,
            'horafina'    => $horaActual,
        ];

        if ($this->hasFile('comprobante_confirmacion')) {
            $datos['comenta3'] = $this->file('comprobante_confirmacion')
                ->store('comprobantes_gestion', 'public');
        }

        return $datos;
    }
}
