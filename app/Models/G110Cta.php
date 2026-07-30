<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class G110Cta extends Model
{
    // Nombre exacto de tu tabla con el guion bajo
    protected $table = 'g110_cta';
    protected $primaryKey = 'cod_deu';

    public function cliente()
    {
        return $this->belongsTo(G110::class, 'cod_deu', 'cod_deu');
    }

    // Atributo calculado para cada registro de detalle/cuota
    public function getTotalDescuentoAttribute()
    {
        // Verifica si dato4 es numérico
        $porcentaje = is_numeric($this->dato4) ? (float)$this->dato4 / 100 : 0;

        $descuento = $this->mon_ini * $porcentaje;

        return round($this->mon_ini - $descuento, 2);
    }

    public function getImporteCalculadoAttribute()
    {
        $porcentaje = is_numeric($this->dato4) ? (float)$this->dato4 / 100 : 0;
        return round($this->mon_ini - ($this->mon_ini * $porcentaje), 2);
    }
}
