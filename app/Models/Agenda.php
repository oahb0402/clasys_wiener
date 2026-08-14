<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    // Indicamos el nombre exacto de la tabla en producción
    protected $table = 'agendas';

    // Desactivar si la tabla no tiene timestamps (created_at / updated_at)
    public $timestamps = false;

    protected $fillable = [
        'cod_deu',
        'fecha',
        'hora',
        'usuario',
        'obs',
        'cartera',
        'cod_ban',
        'usuario_creador',
    ];
}
