<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../paginas/produtos.php");
    exit();
}

require_once("./_sessao.php");

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

    $comandoSQL = $conexao->prepare(
        "INSERT INTO produtos
            (nome, codigo, categoria_id, preco, quantidade, estoque_minimo, descricao, ativo)
         VALUES
            (:nome, NULLIF(:codigo, ''), :categoria_id, :preco, 0, :estoque_minimo, :descricao, 1)"
    );
    $comandoSQL->execute([
        ":nome" => $nome,
        ":codigo" => $codigo,
        ":categoria_id" => $categoriaId,
        ":preco" => $preco,
        ":estoque_minimo" => $estoqueMinimo,
        ":descricao" => $descricao,
    ]);

    header("location: ../paginas/produtos.php?status=sucesso");
    exit();
} catch (PDOException $erro) {
    if (($erro->errorInfo[1] ?? null) == 1062) {
        header("location: ../paginas/produtos.php?status=sku-duplicado");
        exit();
    }

    header("location: ../paginas/produtos.php?status=erro");
    exit();
}
