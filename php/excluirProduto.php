<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../pages/produtos.php");
    exit();
}

require_once("./_sessao.php");

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("location: ../pages/produtos.php?status=invalido");
    exit();
}

try {
    require_once("./conexao.php");

    $comandoSQL = $conexao->prepare(
        "UPDATE produtos SET ativo = 0 WHERE id = :id AND ativo = 1"
    );
    $comandoSQL->execute([":id" => $id]);

    header("location: ../pages/produtos.php?status=excluido");
    exit();
} catch (PDOException $erro) {
    header("location: ../pages/produtos.php?status=erro");
    exit();
}
