<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class G110 extends Model
{
    protected $table = 'g110';
    
    // Si tu llave primaria en g110 es 'cod_deu', ponla aquí. 
    // Si sigue siendo 'id' pero almacena ese número largo, déjala como 'id'
    protected $primaryKey = 'cod_deu'; 

    // ¡ESTA ES LA LÍNEA CLAVE! Le dice a Laravel que el ID es un texto (String)
    protected $keyType = 'string';

    // Desactivamos el auto-incremento ya que es un código manual de texto
    public $incrementing = false;

    // Relación con las cuentas (g110_cta)
    public function detalles()
    {
        // El segundo parámetro es la columna FK en g110_cta (que según el error es 'cod_deu')
        // El tercer parámetro es la llave local en g110 (que asumimos es 'cod_deu')
        return $this->hasMany(G110Cta::class, 'cod_deu', 'cod_deu'); 
    }

    // Relación con las gestiones (g220)
    public function gestiones()
    {
        return $this->hasMany(G220::class, 'cod_deu', 'cod_deu');
    }


    // Relación con las gestiones (g220_sms)
    public function gestiones_sms()
    {
        return $this->hasMany(G220Sms::class, 'cod_deu', 'cod_deu');
    }

    // Atributo calculado para sumar los totales de todas sus cuentas/detalles
    public function getTotalFinalAttribute()
    {
        return $this->detalles
            ->where('estado', '') // Filtra estados vacíos
            ->sum(function ($detalle) {
                return $detalle->total_descuento;
            });
    }
}