<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ContasReceberAgr;
use App\Models\ContasReceberLinkBoletos;
use App\Models\ContratoConsignacao;
use App\Models\ItensConsignacao;
use App\Models\SalesRepresentative;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceiroService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function listarBoletos(string $vencimento): array
    {
        $boletos = ContasReceberLinkBoletos::query()
            ->whereDate('DAT_VENC_CRECEBER', $vencimento)
            ->where('STA_LANCTO_CRECEBER', 'AB')
            ->with([
                'cliente',
                'agrupados:NRO_LANCTO_DESTINO,NRO_LANCTO_CRECEBER',
            ])
            ->get();

        return ['boletos' => $boletos];
    }
}
