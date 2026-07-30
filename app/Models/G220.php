<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class G220 extends Model
{
    protected $table = 'g220';
    protected $primaryKey = 'cod_deu';

    protected $fillable = [
        'cod_deu', 
        'fec_sin'
    ];

    public function cliente()
    {
        return $this->belongsTo(G110::class, 'cod_deu', 'cod_deu');
    }
}