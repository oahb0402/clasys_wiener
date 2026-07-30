<?php

namespace App\Http\Controllers;

use App\Models\G110;
use Illuminate\Support\Facades\DB; // <-- Importamos DB si no usas un modelo para los controles
use App\Models\G220;
use App\Models\G220Sms;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClasysController extends Controller
{
    public function index($id)
    {
        // 1. Buscamos el cliente en la tabla 'g110' con sus cuentas asociadas en 'g110_cta'
        $cliente = G110::with('detalles')->findOrFail($id);

        // Para aplanar todos los recibos de todas sus cuentas en una sola colección fácil de recorrer:
        // 2. Usas directamente la colección de detalles como tus recibos
        $recibos = $cliente->detalles;


        //SELECT substr(codigo,3,3) as codigo1,descri from f190 where codigo like 'TB%' $adicion and codigo in ('TBTM','TBTC','TBML','TBMT','TBWA') order by codigo1

        $tipo_gestiones = DB::table('f190')
            ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
            ->where('activo', '1')
            ->whereIn('codigo', ['TBTM', 'TBTC', 'TBML', 'TBMT', 'TBWA']) // <-- Se cambia 'where' por 'whereIn'
            ->orderBy('descri', 'asc')                   // <-- Ordenado alfabéticamente por descripción
            ->get();


        $tipo_contactos = DB::table('f190')
            ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
            ->where('activo', '1')
            ->where('codigo', 'LIKE', 'UN%')
            ->orderBy('descri', 'asc')                   // <-- Ordenado alfabéticamente por descripción
            ->get();


        $respuestas = DB::table('respuestas')
            ->select('codigo', 'descrip', 'corta', 'promesa')
            ->where('activo', '1') // <-- Filtra solo las respuestas activas
            ->where('tipo', 'TELEFONO') // <-- Filtra solo las respuestas activas
            ->orderBy('corta', 'asc') // Ajustado a 'descrip'
            ->orderBy('descrip', 'asc') // Ajustado a 'descrip'
            ->get()
            ->groupBy('corta');


        // 2. Obtenemos TODAS las sub-respuestas activas incluyendo el campo padre (ej: 'respuesta_codigo' o 'padre_id')
        $sub_respuestas = DB::table('sub_respuestas')
            ->select('codigo', 'descrip')
            ->where('activo', '1')
            ->where('tipo', 'TELEFONO')
            // Quitamos el ->where('codigo', '!=', '12') para que se envíe a la vista
            ->orderByRaw('codigo::int ASC')
            ->get();


        $condiciones = DB::table('condiciong110')
            ->select('codigo', 'descrip')
            ->where('activo', '1') // <-- Filtra solo las respuestas activas
            ->whereNotIn('codigo', ['AC', 'AG', 'AJ', 'AD', 'BQ', 'DB', 'DV', 'DC', 'EP', 'RF', 'SS', 'IF', 'IT', 'IN', 'MN', 'NG', 'NM', 'ND', 'NT', 'PC', 'PT', 'PH', 'PU', 'X1', 'Y1', 'PF', 'PV', 'VF', 'RR', 'RW', 'SM', 'ST', 'UT', 'UF', 'UM', 'UG', 'IC', 'RN', 'UP', 'ZP', 'CA'])
            ->orderBy('descrip', 'asc') // Ajustado a 'descrip'
            ->get();

        // Obtener solo los códigos como un array: ['21', '22', '31']
        $codigosPromesaX = DB::table('r_respuestas_x')
            ->where('tipo', 'PROMESA')
            ->pluck('codigo')
            ->toArray();

        // Obtener solo los códigos como un array: ['21', '22', '31']
        $codigosConfirmacionX = DB::table('r_respuestas_x')
            ->where('tipo', 'CONFIRMACION')
            ->pluck('codigo')
            ->toArray();


        // Pasamos el $id del cliente de forma segura en un arreglo de parámetros
        $telefonosSugeridos = [];
        /* $telefonosSugeridos = DB::select('SELECT * FROM f_telefonos_totales_banco_cod_deu(:cod_deu)', [
        'cod_deu' => $id
        ]); */



        // Obtener el cliente con sus conteos
        $clienteId = $id;
        $historialRapido = [

            // Total de gestiones excluyendo IV y SMS y MAIL
            'total' => $cliente->gestiones()
                ->whereNotIn('tip_con', ['IV', 'ML'])
                ->count(),

            // Gestiones donde hubo contacto efectivo o compromiso (según tu regla de negocio)
            'positives' => $cliente->gestiones()
                ->whereNotIn('tip_con', ['IV', 'CNE'])
                ->whereIn('corta', ['CEF', 'CNE'])
                ->count(),

            // Herramienta IVR (Interactive Voice Response)
            'ivr' => $cliente->gestiones()
                ->where('tip_con', 'IV') // O filtrando por tabla/relación de IVR
                ->count(),

            'mail' => $cliente->gestiones()
                ->whereIn('tip_con', ['ML'])
                ->count(),

            'sms' => $cliente->gestiones_sms()
                ->count(),

            'abono' => 0
        ];




        // 3. Días restantes para el cierre de cartera
        // Tomando como referencia la fecha de hoy
        $fechaCierre = Carbon::create(2026, 12, 31);
        $diasParaCierre = Carbon::now()->diffInDays($fechaCierre);

        // 4. Renderizamos la vista principal pasándole las variables
        return view('crm.principal', compact(
            'cliente',
            'recibos',
            'historialRapido',
            'diasParaCierre',
            'tipo_gestiones',
            'tipo_contactos',
            'respuestas',
            'sub_respuestas',
            'codigosPromesaX',
            'codigosConfirmacionX',
            'condiciones',
            'telefonosSugeridos'
        ));
    }


    public function getHistorialSms(Request $request, $id)
{
    // 1. Obtener la paginación directa (sin 'with' innecesarios en la instancia principal)
    $smsPaginados = G110::findOrFail($id)
        ->gestiones_sms()
        ->orderBy('item', 'desc')
        ->paginate(5);

    // 2. Extraer metadatos UNA sola vez fuera del loop
    $total = $smsPaginados->total();
    $firstItem = $smsPaginados->firstItem();

    // 3. Mapear la colección de forma eficiente
    $data = collect($smsPaginados->items())->map(function ($item, $key) use ($total, $firstItem) {

        // Cálculo del ítem descendente (#15, #14, #13...)
        $numeroItem = $total - ($firstItem + $key - 1);

        // Formateo de Fecha / Hora
        $fechaFormateada = 'N/A';

        if (!empty($item->fec_sin)) {
            $fechaFormateada = is_object($item->fec_sin)
                ? $item->fec_sin->format('Y-m-d H:i:s')
                : trim($item->fec_sin);

            if (!empty($item->control1)) {
                $fechaClean = trim(explode(' ', $fechaFormateada)[0]);
                $horaClean = trim($item->control1);
                $fechaFormateada = "{$fechaClean} {$horaClean}";
            }
        }

        return [
            'item_num'   => $numeroItem,
            'fecha'      => $fechaFormateada,
            'telefono'   => $item->telef_ges ?? 'N/A',
            'estado'     => $item->tip_rb ?? 'SMS',
            'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
        ];
    });

    // 4. Retorno JSON
    return response()->json([
        'data'         => $data,
        'current_page' => $smsPaginados->currentPage(),
        'last_page'    => $smsPaginados->lastPage(),
        'first_item'   => $firstItem ?? 0,
        'last_item'    => $smsPaginados->lastItem() ?? 0,
        'total'        => $total,
    ]);
}
}
