<?php
$comandoTotais = $conexao->query(
    "SELECT
        COUNT(*) AS total_movimentacoes,
        COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN quantidade ELSE 0 END), 0) AS total_entradas,
        COALESCE(SUM(CASE WHEN tipo = 'saida' THEN quantidade ELSE 0 END), 0) AS total_saidas
     FROM movimentacoes"
);
$totais = $comandoTotais->fetch(PDO::FETCH_ASSOC);
$totalMovs = (int) $totais["total_movimentacoes"];
$totalEntradas = (int) $totais["total_entradas"];
$totalSaidas = (int) $totais["total_saidas"];

$comandoMovimentacoes = $conexao->query(
    "SELECT
        m.id,
        m.tipo,
        m.quantidade,
        m.observacao,
        m.data_hora,
        p.nome AS produto_nome,
        p.codigo AS produto_codigo,
        u.nome AS usuario_nome
     FROM movimentacoes m
     INNER JOIN produtos p ON m.produto_id = p.id
     INNER JOIN users u ON m.usuario_id = u.id
     ORDER BY m.data_hora DESC, m.id DESC"
);
$movimentacoes = $comandoMovimentacoes->fetchAll(PDO::FETCH_ASSOC);

$busca = isset($_GET["busca"]) && is_string($_GET["busca"])
    ? trim($_GET["busca"])
    : "";
$tipoInformado = $_GET["tipo"] ?? "";
$tipoFiltro = in_array($tipoInformado, ["entrada", "saida"], true)
    ? $tipoInformado
    : "";
$dataInicioInformada = $_GET["de"] ?? "";
$dataFimInformada = $_GET["ate"] ?? "";
$dataInicio = "";
$dataFim = "";

if (
    is_string($dataInicioInformada)
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInicioInformada)
    && checkdate(
        (int) substr($dataInicioInformada, 5, 2),
        (int) substr($dataInicioInformada, 8, 2),
        (int) substr($dataInicioInformada, 0, 4)
    )
) {
    $dataInicio = $dataInicioInformada;
}

if (
    is_string($dataFimInformada)
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFimInformada)
    && checkdate(
        (int) substr($dataFimInformada, 5, 2),
        (int) substr($dataFimInformada, 8, 2),
        (int) substr($dataFimInformada, 0, 4)
    )
) {
    $dataFim = $dataFimInformada;
}

$movimentacoes = array_values(array_filter($movimentacoes, function ($movimentacao) use ($busca, $tipoFiltro, $dataInicio, $dataFim) {
    $correspondeBusca = $busca === "" || stripos(
        ($movimentacao["produto_nome"] ?? "") . " " . ($movimentacao["produto_codigo"] ?? ""),
        $busca
    ) !== false;
    $correspondeTipo = $tipoFiltro === ""
        || ($movimentacao["tipo"] ?? "") === $tipoFiltro;
    $dataMovimentacao = substr((string) ($movimentacao["data_hora"] ?? ""), 0, 10);
    $correspondeInicio = $dataInicio === "" || $dataMovimentacao >= $dataInicio;
    $correspondeFim = $dataFim === "" || $dataMovimentacao <= $dataFim;

    return $correspondeBusca
        && $correspondeTipo
        && $correspondeInicio
        && $correspondeFim;
}));
