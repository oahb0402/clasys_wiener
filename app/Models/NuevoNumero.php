<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuevoNumero extends Model
{
    // Indicamos el nombre exacto de la tabla en producción
    protected $table = 't_nuevos_numeros';

    // Desactivar si la tabla no tiene timestamps (created_at / updated_at)
    public $timestamps = false;

    protected $fillable = [
        'cod_ban',
        'cod_deu',
        'numero',
        'tipo_telefono',
        'usuario',
    ];
}
