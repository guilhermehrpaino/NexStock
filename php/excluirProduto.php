<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../paginas/produtos.php");
    exit();
}

require_once("./_sessao.php");

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

try {
    require_once("./conexao.php");

    $comandoSQL = $conexao->prepare(
        "UPDATE produtos SET ativo = 0 WHERE id = :id AND ativo = 1"
    );
    $comandoSQL->execute([":id" => $id]);

    header("location: ../paginas/produtos.php?status=excluido");
    exit();
} catch (PDOException $erro) {
    header("location: ../paginas/produtos.php?status=erro");
    exit();
}
