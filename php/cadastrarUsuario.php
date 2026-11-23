<?php
if($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: ../pages/usuarios.php");
    exit();
}

require_once("./_sessao.php");

$nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$usuario = filter_input(INPUT_POST, "usuario", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cargo = filter_input(INPUT_POST, "cargo", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$senha = $_POST["senha"] ?? null;
$confirmarSenha = $_POST["confirmar_senha"] ?? null;


try {
    require_once("./conexao.php");

    $verificaUsuario = $conexao->prepare(
        "SELECT cargo FROM users WHERE usuario = :usuario AND ativo = 1"
    );
    $verificaUsuario->execute(array(
        ":usuario" => $_SESSION["nomeUsuario"]
    ));
    $usuarioLogado = $verificaUsuario->fetch();

    if (!$usuarioLogado || $usuarioLogado["cargo"] != "Administrador") {
        http_response_code(403);
        exit("Acesso permitido somente para administradores.");
    }

    $comandoSQL = $conexao->prepare(
        "INSERT INTO users (nome, usuario, senha, cargo, ativo)
         VALUES (:nome, :usuario, :senha, :cargo, :ativo)"
    );

    $comandoSQL->execute(array(
        ":nome" => $nome,
        ":usuario" => $usuario,
        ":senha" => password_hash($senha, PASSWORD_DEFAULT),
        ":cargo" => $cargo,
        ":ativo" => 1
    ));

    if($comandoSQL->rowCount() > 0) {
        header("location: ../pages/usuarios.php?status=sucesso");
        exit();
    } else {
        echo("Entre em contato com o suporte!");
    }
} catch(PDOException $erro) {
    if (($erro->errorInfo[1] ?? null) == 1062) {
        header("location: ../pages/usuarios.php?status=duplicado");
        exit();
    }

    echo("Entre em contato com o suporte.");
}