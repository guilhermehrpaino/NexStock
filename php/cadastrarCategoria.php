<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../pages/categorias.php");
    exit();
}

require_once ("./_sessao.php");

$nome = $_POST["nome"];
$descricao = $_POST["descricao"];

try {
   require_once("./conexao.php");

    $comandoSQL = $conexao->prepare(
        "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)");
    $comandoSQL->execute([
        ":nome" => $nome,
        ":descricao" => $descricao,
    ]);

    header("location: ../pages/categorias.php?status=sucesso");
    exit();
} catch (PDOException $erro) {
    header("location: ../pages/categorias.php?status=erro");
    exit();
}
