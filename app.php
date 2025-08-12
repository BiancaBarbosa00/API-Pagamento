<?php

use Illuminate\Database\Capsule\Manager as DB;
use GuzzleHttp\Client;

$client = new Client();

$pedidos = DB::table('pedidos as p')
    ->select(
        'p.id',
        'p.valor_total',
        'ps.descricao as situacao_descricao',
        'fp.descricao as forma_descricao',
        'pp.num_cartao',
        'pp.vencimento',
        'pp.codigo_verificacao',
        'pp.nome_portador',
        'g.descricao as gateway_descricao',
        'g.endpoint as baseuri',
        'c.id as cliente_id', // <--
        'c.nome as cliente_nome',
        'c.email as cliente_email',
        'c.cpf_cnpj',
        'c.tipo_pessoa',
        "c.data_nasc"
    )
    ->leftJoin('pedido_situacao as ps', 'p.id_situacao', '=', 'ps.id')
    ->leftJoin('pedidos_pagamentos as pp', 'pp.id_pedido', '=', 'p.id')
    ->leftJoin('formas_pagamento as fp', 'pp.id_formapagto', '=', 'fp.id')
    ->leftJoin('lojas_gateway as lg', 'p.id_loja', '=', 'lg.id_loja')
    ->leftJoin('gateways as g', 'g.id', '=', 'lg.id_gateway')
    ->leftJoin('clientes as c', 'c.id', '=', 'p.id_cliente')
//    ->where('ps.id', 1)
//    ->where('fp.id', 3)
//    ->where('g.id', 1)
    ->get();


function bodyArray($pedido) {
    return [
        "external_order_id" => $pedido->id,
        "amount" => round((float)str_replace(',', '.', $pedido->valor_total), 2),
        "card_number" => $pedido->num_cartao,
        "card_cvv" => "{$pedido->codigo_verificacao}",
        "card_expiration_date" => substr($pedido->vencimento, 5, 2) . substr($pedido->vencimento, 2, 2),
        "card_holder_name" => $pedido->nome_portador,
        "customer" => [
            "external_id" => $pedido->cliente_id,
            "name" => $pedido->cliente_nome,
            "type" => $pedido->tipo_pessoa === "F" ? "individual" : "corporation",
            "email" => $pedido->cliente_email,
            "birthday" => $pedido->data_nasc,
            "documents" => [
                "type" => $pedido->tipo_pessoa === "F" ? "cpf" : "cnpj",
                "number" => $pedido->cpf_cnpj
            ]
        ]
    ];
}

$baseuri = $_ENV['BASEURI'];
$endpoint = $_ENV["ENDPOINT"];
$token = $_ENV["TOKEN"];
//dump($pedidos->toArray());

foreach ($pedidos as $pedido) {

    if ($pedido->forma_descricao == "Cartão de Crédito") {
        if ($pedido->situacao_descricao === "Aguardando Pagamento") {
            if ($pedido->gateway_descricao === "PAGCOMPLETO") {
                $body = bodyArray($pedido);

                try {
                    $uri = "{$baseuri}/{$endpoint}?accessToken={$token}";
                    $res = $client->request("POST", $uri, [
                            "body" => json_encode($body),
                            "headers" => [
                                "Content-Type" => "application/json"
                            ]]
                    );

                    $bodyResponse = $res->getBody()->getContents();
                    $data = json_decode($bodyResponse, true);
                    //dump($data);
                    $updated = DB::table("pedidos_pagamentos")->where("id_pedido", $pedido->id)->update(["retorno_intermediador" => $data]);

                    if ($data["Transaction_code"] === "00") {
                        $updated = DB::table("pedidos")->where("id", $pedido->id)->update(["id_situacao" => 2]);
                    } elseif ($data["Transaction_code"] === "04") {
                        $updated = DB::table("pedidos")->where("id", $pedido->id)->update(["id_situacao" => 3]);
                    }

                    echo "PEDIDO (ID):{$pedido->id} // {$data['Message']}<br>";

                } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                    echo "Erro na requisição: " . $e->getMessage();
                }
            }
        }
    }
}

