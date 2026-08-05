<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuevoCorreo extends Model
{
    // Indicamos el nombre exacto de la tabla en producción
    protected $table = 't_nuevos_correos';

    // Desactivar si la tabla no tiene timestamps (created_at / updated_at)
    public $timestamps = false;

    protected $fillable = [
        'cod_ban',
        'cod_deu',
        'correo',
        'tipo_correo',
        'usuario',
    ];
}
