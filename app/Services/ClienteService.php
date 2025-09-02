<?php

namespace App\Services;

use App\Models\Client;
use App\Models\SalesRepresentative;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function index(Request $request)
    {
        Log::info(Auth()->user());
        return Client::query()
            ->with('ultimaNota')
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
            ->when(Auth()->user()->sales_representative, function ($query) {
                $codResp = SalesRepresentative::where('user_id', Auth()->user()->id)->first();
                $query->where('COD_RESPONSAVEL', $codResp->code_sales);
            })

            ->orderBy('COD_CLI', 'DESC')
            ->get();
    }

    public function show(Request $request)
    {
        $data = Client::query()
            ->from('VW_CLIENTES as vc')
            ->join('PEDIDOS_VENDA as pv', 'vc.COD_CLI', '=', 'pv.COD_CLI')              // INNER (garante pedidos do cliente)
            ->leftJoin('PARAMETROS_FISCAIS as pf', 'pv.COD_PARAM_FISCAL', '=', 'pf.COD_PARAM_FISCAL') // LEFT
            ->leftJoin('NF_MESTRE as nfm', 'pv.COD_PED_VENDA', '=', 'nfm.COD_PED_VENDA')             // LEFT
            ->leftJoin('USUARIOS as u', 'vc.COD_RESPONSAVEL', '=', 'u.COD_USU')             // LEFT
            ->where('pv.COD_CLI', $request->get('id'))
            ->select([
                'vc.*',
                'pv.COD_PED_VENDA',
                'pv.DAT_ULT_ATL',
                'pf.DESC_PARAM_FISCAL',
                'pv.QTD_ITENS_TOTAL',
                'pv.QTD_ITENS_ATENDIDOS',
                'pv.VLR_TOTAL_LIQUIDO',
                'pv.STATUS_PEDIDO_VENDA',
                'nfm.NRO_NOTA_FISCAL',
                'u.NOM_USU'
            ])
            ->addSelect([
                // Data da última NF (por cliente) com COD_NATUREZA = '6.113'
                'ULTIMA_NF_DATA' => DB::connection('sqlsrv')
            ->table('NF_MESTRE as nfm2')
            ->selectRaw('TOP 1 nfm2.DAT_EMISSAO_NF')
            ->whereColumn('nfm2.COD_CLI', 'vc.COD_CLI')
            ->where('nfm2.COD_NATUREZA', 6.113) // se for decimal no banco, deixe sem aspas
            ->latest('DAT_EMISSAO_NF')
            // ->orderByDesc('nfm2.DAT_EMISSAO_NF'),
            ])
            ->orderBy('pv.DAT_PEDIDO', 'DESC')
            ->limit(20)
            ->get();

        if (
            Auth()->user()->sales_representative &&
            isset($data[0]) &&
            $data[0]['COD_RESPONSAVEL'] !=
            SalesRepresentative::where('user_id', Auth()->user()->id)->first()->code_sales
        ) {
            throw new Exception("Este cliente não pertence ao vendedor");
        }
        return $data;
    }

    public function getInfos(int $codCli): object
    {
        return Client::where('COD_CLI', $codCli)->first();
    }
}
