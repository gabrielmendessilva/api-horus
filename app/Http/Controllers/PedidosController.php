<?php

namespace App\Http\Controllers;

use App\Services\PedidosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function listarItensPedidos(Request $request, PedidosService $pedidoService): JsonResponse
    {
        try {
            return response()->json($pedidoService->getItens($request->get('id')), 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
