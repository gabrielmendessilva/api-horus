<?php

namespace App\Console\Commands;

use App\Models\SalesRepresentative;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InsertGeral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-geral';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SalesRepresentative::create([
            'user_id' =>3,
            'code_sales' =>140,
        ]);

        User::where('id', 2)
        ->update([
            'password' => Hash::make('M@rcelo2025')
        ]);
    }
}
