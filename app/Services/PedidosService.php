<?php

namespace App\Services;

use App\Models\ItensPedidoVenda;
use Illuminate\Support\Facades\DB;

class PedidosService
{
    public function getItens(int $id)
    {
        return ItensPedidoVenda::query()
            ->from('ITENS_PEDIDO_VENDA as ipv ')
            ->join('view_product as vp', 'ipv.COD_ITEM', '=', 'vp.code ')
            ->where('ipv.COD_PED_VENDA', $id)
            ->select(
                'vp.sku as codigo',
                'vp.name as titulo',
                'ipv.QT_PEDIDA  as qtd_pedido',
                'ipv.QTD_ATENDIDA as qtd_atendida',
                'ipv.VLR_PRECO as valor_unit_bruto',
                'ipv.VLR_DESCONTO as desconto',
                'ipv.VLR_LIQUIDO as valor_unit_liquido'
            )
            ->addSelect(DB::raw('(ipv.VLR_LIQUIDO * ipv.QTD_ATENDIDA) AS valor_total_liquido'))
            ->get();
    }
}
