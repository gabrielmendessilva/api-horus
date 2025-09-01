<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidosVenda extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "PEDIDOS_VENDA";
}
