<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContasReceberLinkBoletos extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "LANCTOS_CRECEBERA";

    protected $appends = ['agrupado'];
    protected $hidden  = ['agrupados']; // esconde a relação no JSON

    public function agrupados()
    {
        return $this->hasMany(
            ContasReceberAgr::class,
            'NRO_LANCTO_DESTINO',   // FK no filho
            'NRO_LANCTO_CRECEBER'   // chave local (única)
        );
    }

    public function cliente()
    {
        return $this->belongsTo(Client::class, 'COD_CLI', 'COD_CLI');
    }

    public function getAgrupadoAttribute()
    {
        if ($this->relationLoaded('agrupados')) {
            return $this->agrupados->pluck('NRO_LANCTO_CRECEBER')->values()->all();
        }
        return $this->agrupados()->pluck('NRO_LANCTO_CRECEBER')->toArray();
    }
}

