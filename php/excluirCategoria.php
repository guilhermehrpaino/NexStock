<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../pages/categorias.php");
    exit();
}

require_once("./_sessao.php");

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("location: ../pages/categorias.php?status=invalida");
    exit();
}

try {
    require_once("./conexao.php");

    $comandoSQL = $conexao->prepare(
        "DELETE FROM categorias WHERE id = :id"
    );
    $comandoSQL->execute([":id" => $id]);

    header("location: ../pages/categorias.php?status=excluida");
    exit();
} catch (PDOException $erro) {
    if (($erro->errorInfo[1] ?? null) == 1451) {
        header("location: ../pages/categorias.php?status=em-uso");
        exit();
    }

    header("location: ../pages/categorias.php?status=erro");
    exit();
}
