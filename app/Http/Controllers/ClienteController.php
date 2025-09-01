<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{

    private ClienteService $clientService;

    public function __construct()
    {
        $this->clientService = new ClienteService();
    }

    public function index(Request $request): JsonResponse
    {
        try {
            Log::info($request->all());
            $data = $this->clientService->index($request);
            return response()
                ->json($data, 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()
                ->json([
                    'message' => $th->getMessage()], 400);
        }
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $data = $this->clientService->show($request);
            return response()
                ->json($data, 200);
        } catch (\Throwable $th) {
            return response()
                ->json($th->getMessage(), 400);
        }
    }
}
