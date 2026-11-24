<?php
$comandoProdutos = $conexao->query(
    "SELECT
        COUNT(*) AS total_produtos,
        COALESCE(SUM(
            CASE
                WHEN estoque_minimo > 0 AND quantidade <= estoque_minimo THEN 1
                ELSE 0
            END
        ), 0) AS produtos_estoque_baixo
     FROM produtos
     WHERE ativo = 1"
);
$totaisProdutos = $comandoProdutos->fetch();
$totalProdutos = (int) $totaisProdutos["total_produtos"];
$produtosEstoqueBaixo = (int) $totaisProdutos["produtos_estoque_baixo"];

$comandoMovimentacoes = $conexao->query(
    "SELECT
        COUNT(*) AS total_movimentacoes,
        COALESCE(SUM(CASE WHEN DATE(data_hora) = CURDATE() THEN 1 ELSE 0 END), 0) AS movimentacoes_hoje
     FROM movimentacoes"
);
$totaisMovimentacoes = $comandoMovimentacoes->fetch();
$totalMovimentacoes = (int) $totaisMovimentacoes["total_movimentacoes"];
$movimentacoesHoje = (int) $totaisMovimentacoes["movimentacoes_hoje"];

$comandoEstoqueBaixo = $conexao->query(
    "SELECT
        p.nome,
        p.quantidade,
        p.estoque_minimo,
        c.nome AS categoria
     FROM produtos p
     INNER JOIN categorias c ON p.categoria_id = c.id
     WHERE p.ativo = 1
       AND p.estoque_minimo > 0
       AND p.quantidade <= p.estoque_minimo
     ORDER BY p.quantidade ASC, p.nome ASC
     LIMIT 5"
);
$produtosBaixoEstoque = $comandoEstoqueBaixo->fetchAll();

$comandoRecentes = $conexao->query(
    "SELECT
        p.nome AS produto,
        m.tipo,
        m.quantidade,
        m.data_hora
     FROM movimentacoes m
     INNER JOIN produtos p ON m.produto_id = p.id
     ORDER BY m.data_hora DESC, m.id DESC
     LIMIT 5"
);
$movimentacoesRecentes = $comandoRecentes->fetchAll();
