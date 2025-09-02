<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensConsignacao extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "VW_ITENS_CONSIGNACAO";
}
