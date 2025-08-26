<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    use HasFactory;

    protected $connection = 'sqlsrv';
    protected $table = "VW_CLIENTES";

    public function ultimaNota()
    {
        return $this->hasOne(NfMestre::class, 'COD_CLI', 'COD_CLI')
                    ->where('COD_NATUREZA', '6.113')
                    ->latest('DAT_EMISSAO_NF');
    }
}
