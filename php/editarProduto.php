<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../pages/produtos.php");
    exit();
}

require_once("./_sessao.php");

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$nome = $_POST["nome"];
$codigo = $_POST["codigo"];
$descricao = $_POST["descricao"];
$categoriaId = filter_input(INPUT_POST, "categoria_id", FILTER_VALIDATE_INT);
$preco = filter_input(INPUT_POST, "preco", FILTER_VALIDATE_FLOAT);
$estoqueMinimo = filter_input(INPUT_POST, "estoque_minimo", FILTER_VALIDATE_INT);

try {
    require_once("./conexao.php");

    $verificaCategoria = $conexao->prepare(
        "SELECT id FROM categorias WHERE id = :id"
    );
    $verificaCategoria->execute([":id" => $categoriaId]);

    if (!$verificaCategoria->fetch()) {
        header("location: ../pages/produtos.php?status=invalido");
        exit();
    }

    $comandoSQL = $conexao->prepare(
        "UPDATE produtos
         SET nome = :nome,
             codigo = :codigo,
             categoria_id = :categoria_id,
             preco = :preco,
             estoque_minimo = :estoque_minimo,
             descricao = :descricao
         WHERE id = :id AND ativo = 1"
    );
    $comandoSQL->execute([
        ":id" => $id,
        ":nome" => $nome,
        ":codigo" => $codigo,
        ":categoria_id" => $categoriaId,
        ":preco" => $preco,
        ":estoque_minimo" => $estoqueMinimo,
        ":descricao" => $descricao,
    ]);

    header("location: ../pages/produtos.php?status=sucesso");
    exit();
} catch (PDOException $erro) {
    if (($erro->errorInfo[1] ?? null) == 1062) {
        header("location: ../pages/produtos.php?status=sku-duplicado");
        exit();
    }

    header("location: ../pages/produtos.php?status=erro");
    exit();
}
