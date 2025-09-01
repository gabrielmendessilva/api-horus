<?php

namespace App\Http\Controllers;

use App\Models\ContratoConsignacao;
use Illuminate\Http\Request;

class ConsignacaoController extends Controller
{
    public function listarContratos(Request $request)
    {
        try {
            $clienteId = $request->get('cliente');
            $contratos = ContratoConsignacao::where('COD_CLI', $clienteId)
                ->where('STA_CTR', 'B')
                ->get();
            return response()->json( $contratos, 200);
        } catch (\Throwable $th) {
            return response()->json(['message'=>$th->getMessage()], 400);

        }
    }
}
