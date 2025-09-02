<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ContratoConsignacao;
use App\Models\ItensConsignacao;
use App\Models\SalesRepresentative;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FinanceiroService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function getContrato(int $id): object
    {
        $contratos = ContratoConsignacao::where('COD_CLI', $id)
            ->where('STA_CTR', 'B')
            ->get();

        return $contratos;
    }

    public function getItens(int $contrato): object
    {
        return ItensConsignacao::query()
            ->from('VW_ITENS_CONSIGNACAO as vic')
            ->join('view_product as vp', 'vic.COD_ITEM', 'vp.code')
            ->where('vic.COD_CTR', $contrato)
            ->select(
                'vic.COD_CLI',
                'vic.COD_ITEM',
                'vic.NOME_ITEM',
                'vp.sku',
                'vic.SALDO',
                'vic.VLR_PRECO',
                'vic.VLR_DESCONTO'
            )->get();
    }
}
