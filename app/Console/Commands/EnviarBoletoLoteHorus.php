<?php

namespace App\Console\Commands;

use App\Mail\BoletoHorusMail;
use App\Models\ContasReceberAgr;
use App\Models\NfMestre;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarBoletoLoteHorus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar-boleto-lote-horus {vencimento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vencimento = $this->argument('vencimento');
        ContasReceberAgr::whereRaw("CONVERT(DATE, CONVERT(DATETIME, DAT_VENC_CRECEBER, 120)) = ?", [$vencimento])
            ->distinct('NRO_LANCTO_DESTINO')
            ->get()
            ->each(function (ContasReceberAgr $nroLanc) {
                try {
                    $filePath = "//192.168.0.99/mnt/HORUS/EXE/Boletos/0101000000{$nroLanc->NRO_LANCTO_DESTINO}01.pdf";
                    echo $nroLanc->COD_NF . " - " . $nroLanc->DAT_VENC_CRECEBER . PHP_EOL;
                    if (!file_exists($filePath)) {
                        throw new Exception("Arquivo não encontrado em: {$filePath}");
                    }
                    $notaFiscal = $this->getDanfes($nroLanc->NRO_LANCTO_DESTINO);
                    $boleto = [
                        'vencimento' => $nroLanc->DAT_VENC_CRECEBER,
                        'valor' => $notaFiscal['total_lanc'],
                    ];
                    Mail::to(['mendes.gabriel@icloud.com'])
                        ->send(new BoletoHorusMail($boleto, $notaFiscal['notas'], $filePath));
                } catch (Exception $e) {
                    echo $e->getMessage() . PHP_EOL;
                    return;
                }
            });
    }

    private function getDanfes($nroLanctAgr)
    {
        $data = [];
        $valorTotal = 0;
        ContasReceberAgr::where('NRO_LANCTO_DESTINO', $nroLanctAgr)
            ->get()
            ->each(function (ContasReceberAgr $nroLanc) use (&$data, &$valorTotal) {
                $nota = NfMestre::where('COD_NF', $nroLanc->COD_NF)->first();
                $valorTotal += (float) $nota->VLR_LIQUIDO_NF;
                $data[] = [
                    'numero' => $nota->NRO_NOTA_FISCAL,
                    'data'   => $nota->DAT_EMISSAO_NF,
                    'valor'  => $nota->VLR_LIQUIDO_NF,
                    'path_nota' => "/mnt/z/NF-e/Saída/PDF/$nota->CHAVE_ACESSO_NFE-nfe.pdf"
                ];
            });
        return [
            'notas' => $data,
            'total_lanc' => $valorTotal
        ];
    }
}
