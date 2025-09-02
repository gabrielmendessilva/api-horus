<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ContasReceberAgr;
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
        $boletos = ContasReceberAgr::whereRaw("CONVERT(DATE, CONVERT(DATETIME, DAT_VENC_CRECEBER, 120)) = ?", [$vencimento])
            ->where('STA_LANCTO_CRECEBER', 'AB')
            ->with('cliente')
            ->distinct('NRO_LANCTO_DESTINO')
            ->get();

        $lancamentosIndividuais = $this->boletosLancamentos($vencimento);

        return [
            'boletos' => $boletos,
            'lancamentosIndividuais' => $lancamentosIndividuais,
        ];
    }

    private function boletosLancamentos(string $vencimento): object
    {
        return ContasReceberAgr::query()
            ->select(['NRO_LANCTO_DESTINO', DB::raw("STRING_AGG(CONVERT(varchar(50), NRO_LANCTO_CRECEBER), ',') AS nros_individuais_csv")])
            ->whereRaw("CONVERT(date, TRY_CONVERT(datetime, DAT_VENC_CRECEBER, 120)) = ?", [$vencimento])
            ->where('STA_LANCTO_CRECEBER', 'AB')
            ->groupBy('NRO_LANCTO_DESTINO')
            ->get();
    }
}
