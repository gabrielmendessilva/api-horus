<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesRepresentative extends Model
{
    protected $connection = 'pgsql';
    protected $table = "sales_representative";

    protected $fillable = [
        'user_id',
        'code_sales'
    ];
}
