<?php

namespace App\Http\Controllers;

use App\Services\ConsignacaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsignacaoController extends Controller
{
    private ConsignacaoService $consignacaoService;

    public function __construct()
    {
        $this->consignacaoService = new ConsignacaoService();
    }

    public function listarContratos(Request $request): JsonResponse
    {
        try {
            $clienteId = $request->get('cliente');
            $contratos = $this->consignacaoService->getContrato($clienteId);
            return response()->json($contratos, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    public function gerarMapa(Request $request):JsonResponse
    {
        try {
            $itens = $this->consignacaoService->getItens($request->get('contrato'));
            return response()->json($itens, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
