<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../paginas/movimentacoes.php");
    exit();
}

require_once("./_sessao.php");

$produtoId = filter_input(INPUT_POST, "produto_id", FILTER_VALIDATE_INT);
$tipo = $_POST["tipo"];
$quantidade = filter_input(INPUT_POST, "quantidade", FILTER_VALIDATE_INT);
$observacao = $_POST["observacao"];


try {
    require_once("./conexao.php");

    $comandoUsuario = $conexao->prepare(
        "SELECT id FROM users WHERE usuario = :usuario AND ativo = 1"
    );
    $comandoUsuario->execute([":usuario" => $_SESSION["nomeUsuario"]]);
    $usuarioId = $comandoUsuario->fetchColumn();

    $conexao->beginTransaction();

    $comandoProduto = $conexao->prepare(
        "SELECT quantidade
         FROM produtos
         WHERE id = :id AND ativo = 1
         FOR UPDATE"
    );
    $comandoProduto->execute([":id" => $produtoId]);
    $produto = $comandoProduto->fetch();

    $estoqueAtual = (int) $produto["quantidade"];

    if ($tipo === "saida" && $quantidade > $estoqueAtual) {
        $conexao->rollBack();
        header("location: ../paginas/movimentacoes.php?status=estoque-insuficiente");
        exit();
    }

    if ($tipo === "entrada") {
        $novoEstoque = $estoqueAtual + $quantidade;
    } else {
        $novoEstoque = $estoqueAtual - $quantidade;
    }

    $comandoEstoque = $conexao->prepare(
        "UPDATE produtos SET quantidade = :quantidade WHERE id = :id"
    );
    $comandoEstoque->execute([
        ":quantidade" => $novoEstoque,
        ":id" => $produtoId,
    ]);

    $comandoMovimentacao = $conexao->prepare(
        "INSERT INTO movimentacoes
            (produto_id, usuario_id, tipo, quantidade, observacao)
         VALUES
            (:produto_id, :usuario_id, :tipo, :quantidade, :observacao)"
    );
    $comandoMovimentacao->execute([
        ":produto_id" => $produtoId,
        ":usuario_id" => $usuarioId,
        ":tipo" => $tipo,
        ":quantidade" => $quantidade,
        ":observacao" => $observacao,
    ]);

    $conexao->commit();

    header("location: ../paginas/movimentacoes.php?status=sucesso");
    exit();
} catch (PDOException $erro) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    header("location: ../paginas/movimentacoes.php?status=erro");
    exit();
}
