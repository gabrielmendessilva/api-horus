<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfMestre extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "NF_MESTRE";
}
