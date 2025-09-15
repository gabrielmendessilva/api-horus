<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContasReceber extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "LANCTOS_CRECEBER";

    public function cliente()
    {
        return $this->belongsTo(Client::class, 'COD_CLI', 'COD_CLI');
    }
}
