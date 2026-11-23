<?php

$categorias = $conexao->query(
    "SELECT id, nome FROM categorias ORDER BY nome"
)->fetchAll();

$comandoProdutos = $conexao->query(
    "SELECT p.*, c.nome AS categoria_nome
     FROM produtos p
     LEFT JOIN categorias c ON p.categoria_id = c.id
     WHERE p.ativo = 1
     ORDER BY p.nome ASC"
);
$produtos = $comandoProdutos->fetchAll();

$produtoEdicao = null;
$idEdicao = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT);
$busca = isset($_GET["busca"]) && is_string($_GET["busca"])
    ? trim($_GET["busca"])
    : "";
$categoriaFiltro = isset($_GET["categoria"]) && is_scalar($_GET["categoria"])
    ? (string) $_GET["categoria"]
    : "";
$estoqueInformado = $_GET["estoque"] ?? "";
$estoqueFiltro = in_array($estoqueInformado, ["normal", "baixo", "zero"], true)
    ? $estoqueInformado
    : "";

if ($idEdicao) {
    $comandoEdicao = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = :id AND ativo = 1"
    );
    $comandoEdicao->execute([":id" => $idEdicao]);
    $produtoEdicao = $comandoEdicao->fetch(PDO::FETCH_ASSOC) ?: null;
}

$produtos = array_values(array_filter($produtos, function ($produto) use ($busca, $categoriaFiltro, $estoqueFiltro) {
    $correspondeBusca = $busca === "" || stripos(
        ($produto["nome"] ?? "") . " " . ($produto["codigo"] ?? ""),
        $busca
    ) !== false;
    $correspondeCategoria = $categoriaFiltro === ""
        || (string) ($produto["categoria_id"] ?? "") === $categoriaFiltro;
    $quantidade = (int) ($produto["quantidade"] ?? 0);
    $minimo = (int) ($produto["estoque_minimo"] ?? 0);
    $correspondeEstoque = $estoqueFiltro === ""
        || ($estoqueFiltro === "zero" && $quantidade === 0)
        || ($estoqueFiltro === "baixo" && $quantidade > 0 && $quantidade <= $minimo)
        || ($estoqueFiltro === "normal" && $quantidade > $minimo);

    return $correspondeBusca && $correspondeCategoria && $correspondeEstoque;
}));
