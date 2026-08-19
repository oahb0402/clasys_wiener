<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnvMail extends Model
{
    use HasFactory;
    // Indicamos el nombre exacto de la tabla en producción
    protected $table = 't_env_mail';
    // Desactivar si la tabla no tiene timestamps (created_at / updated_at)
    #public $timestamps = false;
    public $timestamps = true;

    protected $fillable = [
        'cod_deu',
        'cod_ban',
        'grupo',
        'nom_deu',
        'nro_doc',
        'monto_pago',
        'mon_ini',
        'porcentaje',
        'fecha_pago',
        'created_by'
    ];
}
