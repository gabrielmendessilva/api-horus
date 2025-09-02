<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConsignacaoController;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function (Request $request) {
    return response()->json(Client::first()->toArray());
});



Route::post('/refresh', [AuthController::class, 'refreshToken']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/verify-2fa', [AuthController::class, 'verify2FA']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::group(['middleware' => ['jwt.auth']], function () {
    Route::prefix('cliente')->group(function () {
        Route::post('index', [ClienteController::class, 'index']);
        Route::post('getInfos', [ClienteController::class, 'getInfos']);
        Route::post('show', [ClienteController::class, 'show']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('me', function () {
            return auth()->user();
        });
    });
    Route::prefix('consignacao')->group(function () {
        Route::post('/contratos', [ConsignacaoController::class, 'listarContratos'])
            ->name('consignacao.contratos');

        Route::post('/mapa', [ConsignacaoController::class, 'gerarMapa'])
            ->name('consignacao.mapa');
    });
});
