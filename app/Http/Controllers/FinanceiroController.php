<?php

namespace App\Http\Controllers;

use App\Services\FinanceiroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    public function listarBoletos(Request $request, FinanceiroService $financeiroService): JsonResponse
    {
        try {
            $data = $financeiroService->listarBoletos($request->get('vencimento'));
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
