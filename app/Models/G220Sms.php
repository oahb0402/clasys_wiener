<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class G220Sms extends Model
{
    protected $table = 'g220_sms';
    protected $primaryKey = 'cod_deu';

    protected $fillable = [
        'cod_deu', 
        'fec_sin'
    ];

    public function cliente_sms()
    {
        return $this->belongsTo(G110::class, 'cod_deu', 'cod_deu');
    }
}