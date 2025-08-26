<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;

class ClienteService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function index(Request $request)
    {
        return Client::query()
            ->with('ultimoAcerto')
            ->when($request->get('nome'), function ($query, $nome) {
                $query->where('NOME_CLI', 'like', '%' . $nome . '%');
            })
            ->when($request->get('email'), function ($query, $email) {
                $query->where('EMAIL', 'like', '%' . $email . '%');
            })
            ->when($request->get('cnpj'), function ($query, $cnpj) {
                $query->where('CNPJ', $cnpj);
            })
            ->when($request->get('cpf'), function ($query, $cpf) {
                $query->where('CPF', $cpf);
            })
            ->when($request->get('cidade'), function ($query, $cidade) {
                $query->where('NOM_LOCAL', 'like', '%' . $cidade . '%');
            })
            ->when($request->get('top'), function ($query, $top) {
                $query->take(10);
            })
            ->get();
    }
}
