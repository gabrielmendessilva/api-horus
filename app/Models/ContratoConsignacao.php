<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoConsignacao extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "CONTRATO";
}
