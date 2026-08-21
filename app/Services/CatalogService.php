<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CatalogService
{

    /**
     * Devuelve todos los catálogos en un solo arreglo estructurado.
     */
    public function obtenerCatalogos(): array
    {
        return [
            'tipo_gestiones' => $this->obtenerTipoGestiones(),
            'tipo_contactos' => $this->obtenerTipoContactos(),
            'respuestas'     => $this->obtenerRespuestas(),
            'sub_respuestas' => $this->obtenerSubRespuestas(),
            'condiciones'    => $this->obtenerCondiciones(),
        ];
    }

    public function obtenerTipoGestiones(): array
    {
        return Cache::remember('catalogo_tipo_gestiones', 86400, function () {
            return DB::table('f190')
                ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
                ->where('activo', '1')
                ->whereIn('codigo', ['TBTM', 'TBTC', 'TBML', 'TBMT', 'TBWA'])
                ->orderBy('descri', 'asc')
                ->get()
                ->map(fn($item) => (array) $item)
                ->toArray();
        });
    }

    public function obtenerTipoContactos(): array
    {
        return Cache::remember('catalogo_tipo_contactos', 86400, function () {
            return DB::table('f190')
                ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
                ->where('activo', '1')
                ->where('codigo', 'LIKE', 'UN%')
                ->orderBy('descri', 'asc')
                ->get()
                ->map(fn($item) => (array) $item)
                ->toArray();
        });
    }

    public function obtenerRespuestas(): array
    {
        return Cache::remember('catalogo_respuestas_telefono', 86400, function () {
            return DB::table('respuestas')
                ->select('codigo', 'descrip', 'corta', 'promesa')
                ->where('activo', '1')
                ->where('tipo', 'TELEFONO')
                ->orderBy('corta', 'asc')
                ->orderBy('descrip', 'asc')
                ->get()
                ->map(fn($item) => (array) $item)
                ->groupBy('corta')
                ->toArray();
        });
    }

    public function obtenerSubRespuestas(): array
    {
        return Cache::remember('catalogo_sub_respuestas_telefono', 86400, function () {
            return DB::table('sub_respuestas')
                ->select('codigo', 'descrip')
                ->where('activo', '1')
                ->where('tipo', 'TELEFONO')
                ->orderByRaw('codigo::int ASC')
                ->get()
                ->map(fn($item) => (array) $item)
                ->toArray();
        });
    }

    public function obtenerCondiciones(): array
    {
        return Cache::remember('catalogo_condiciones_activas', 86400, function () {
            return DB::table('condiciong110')
                ->select(DB::raw('TRIM(codigo) as codigo'), 'descrip')
                ->where('activo', '1')
                ->whereNotIn(DB::raw('TRIM(codigo)'), ['AC', 'AG', 'AJ', 'AD', 'BQ', 'DB', 'DV', 'DC', 'EP', 'RF', 'SS', 'IF', 'IT', 'IN', 'MN', 'NG', 'NM', 'ND', 'NT', 'PC', 'PT', 'PH', 'PU', 'X1', 'Y1', 'PF', 'PV', 'VF', 'RR', 'RW', 'SM', 'ST', 'UT', 'UF', 'UM', 'UG', 'IC', 'RN', 'UP', 'ZP', 'CA'])
                ->orderBy('descrip', 'asc')
                ->get()
                ->map(fn($item) => (array) $item)
                ->toArray();
        });
    }


    public function obtenerFechaCierre(?string $grupo): Carbon
    {
        // Limpiamos la variable por si viene con espacios
        $grupoClean = trim($grupo ?? '');

        // Clave de caché única por grupo (ej. parametro_fecha_cierre_GRUPO01)
        $cacheKey = "parametro_fecha_cierre_{$grupoClean}";

        $fechaString = Cache::remember($cacheKey, 86400, function () use ($grupoClean) {
            return DB::table('grupo_vigente')
                ->where('grupo', $grupoClean)
                ->value('fec_fin'); // Devuelve el string de la fecha (ej. '2026-12-31')
        });

        // Retornamos la fecha parseada o un fallback seguro
        return $fechaString ? Carbon::parse($fechaString) : Carbon::create(2026, 12, 31);
    }

    /**
     * Obtiene los códigos de respuestas de tipo PROMESA
     */
    public function obtenerCodigosPromesa(): array
    {
        return Cache::remember('codigos_promesa_x', 86400, function () {
            return DB::table('r_respuestas_x')
                ->where('tipo', 'PROMESA')
                ->pluck('codigo')
                ->toArray();
        });
    }

    /**
     * Obtiene los códigos de respuestas de tipo CONFIRMACION
     */
    public function obtenerCodigosConfirmacion(): array
    {
        return Cache::remember('codigos_confirmacion_x', 86400, function () {
            return DB::table('r_respuestas_x')
                ->where('tipo', 'CONFIRMACION')
                ->pluck('codigo')
                ->toArray();
        });
    }
}
