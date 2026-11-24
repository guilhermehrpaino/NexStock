<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../paginas/categorias.php");
    exit();
}

require_once("./_sessao.php");

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$nome = $_POST["nome"];
$descricao = $_POST["descricao"];


try {
    require_once("./conexao.php");

    $comandoSQL = $conexao->prepare(
        "UPDATE categorias
         SET nome = :nome, descricao = :descricao)
         WHERE id = :id"
    );
    $comandoSQL->execute([
        ":id" => $id,
        ":nome" => $nome,
        ":descricao" => $descricao,
    ]);

    header("location: ../paginas/categorias.php?status=sucesso");
    exit();
} catch (PDOException $erro) {
    header("location: ../paginas/categorias.php?status=erro");
    exit();
}
