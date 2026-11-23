<?php

$comandoCategorias = $conexao->query(
    "SELECT c.*, COUNT(p.id) AS total_produtos
     FROM categorias c
     LEFT JOIN produtos p ON p.categoria_id = c.id
     GROUP BY c.id
     ORDER BY c.nome ASC"
);
$categorias = $comandoCategorias->fetchAll();
$totalCategorias = count($categorias);
$categoriaEdicao = null;
$idEdicao = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT);
$busca = isset($_GET["busca"]) && is_string($_GET["busca"])
    ? trim($_GET["busca"])
    : "";

if ($idEdicao) {
    $comandoEdicao = $conexao->prepare(
        "SELECT * FROM categorias WHERE id = :id"
    );
    $comandoEdicao->execute([":id" => $idEdicao]);
    $categoriaEdicao = $comandoEdicao->fetch() ?: null;
}

if ($busca !== "") {
    $categorias = array_values(array_filter($categorias, function ($categoria) use ($busca) {
        return stripos(
            $categoria["nome"] . " " . ($categoria["descricao"] ?? ""),
            $busca
        ) !== false;
    }));
}
