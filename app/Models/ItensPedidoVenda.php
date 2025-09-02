<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensPedidoVenda extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "ITENS_PEDIDO_VENDA";
}
