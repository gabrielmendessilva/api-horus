<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Boleto - Editora Martin Claret</title>
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        font-family: Arial, sans-serif;
    }
    /* Container externo para centralizar o card */
    table.body-wrapper {
        width: 100%;
        background-color: #f4f4f4;
        padding: 20px 0;
    }
    .container {
        max-width: 600px;
        width: 100%;
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .header {
        background-color: #003366;
        padding: 20px;
        text-align: center;
    }
    .content {
        padding: 25px;
        color: #333333;
        line-height: 1.5;
    }
    .content h2 {
        color: #003366;
        margin-bottom: 15px;
    }
    .boleto-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .boleto-table th, .boleto-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .boleto-table th {
        background-color: #003366;
        color: white;
    }
    .btn-download {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background-color: #ff6600;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
    }
    .footer {
        text-align: center;
        font-size: 12px;
        color: #777;
        padding: 15px;
        background-color: #f4f4f4;
    }

    @media screen and (max-width: 620px) {
        .container {
            width: 90% !important;
        }
        .content {
            padding: 15px !important;
        }
        .btn-download {
            width: 100%;
            text-align: center;
        }
    }
</style>
</head>
<body>
<!-- Tabela externa para centralização -->
<table class="body-wrapper" cellpadding="0" cellspacing="0">
<tr>
    <td align="center">
        <!-- Card principal -->
        <table class="container" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header">
                    <img src="{{ $message->embed($logoPath) }}" alt="Logo Editora Martin Claret" width="200">
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h2>Prezado,</h2>
                    <div class="message">
                        Lembramos que suas faturas são enviadas por e-mail. Para assegurar o recebimento de suas faturas, pedimos que mantenha atualizado junto ao nosso setor de faturamento seus endereços de e-mail. Você pode cadastrar até 3 (três) e-mails para isso.<br><br>
                        Caso haja a necessidade de alguma correção em sua fatura, entre em contato com nosso financeiro até 5 (cinco) dias de antecedência ao vencimento para as devidas correções.
                    </div>

                    <!-- Tabela do boleto -->
                    <table class="boleto-table">
                        <tr>
                            <th>Descrição</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                        </tr>
                        <tr>
                            <td>Boleto </td>
                            <td>{{ \Carbon\Carbon::parse($boleto['vencimento'])->format('d/m/Y') }}</td>
                            <td>{{ number_format($boleto['valor'], 2, ',', '.') }}</td>
                        </tr>
                    </table>

                    @if(!empty($notas))
                        <h3>Notas fiscais incluídas neste boleto:</h3>
                        <table class="boleto-table">
                            <tr>
                                <th>Número</th>
                                <th>Data</th>
                                <th>Valor</th>
                            </tr>
                            @foreach($notas as $nota)
                                <tr>
                                    <td>{{ $nota['numero'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($nota['data'])->format('d/m/Y') }}</td>
                                    <td>{{ number_format($nota['valor'], 2, ',', '.')  }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif

                    <!-- <a href="#" class="btn-download">Baixar Boleto</a> -->
                </td>
            </tr>
        </table>
        <!-- Fim card -->
    </td>
</tr>
</table>

<div class="footer">
    Editora Martin Claret LTDA - Todos os direitos reservados
</div>
</body>
</html>
