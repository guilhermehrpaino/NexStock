<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
        
    $usuario = filter_input(INPUT_POST, "usuario", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = $_POST["senha"];

    try {
        require_once("./conexao.php");

        $comandoSQL = $conexao->prepare("SELECT * FROM users WHERE usuario = :usuario");

        $comandoSQL->execute(array(
            ":usuario" => $usuario
        ));

        $dados = $comandoSQL->fetch();

        if($comandoSQL->rowCount() > 0) {
            if(password_verify($senha, $dados["senha"])) {
                session_start();
                $_SESSION["nomeUsuario"] = $dados["usuario"];
                $_SESSION["usuario_nome"] = $dados["nome"];
                $_SESSION["usuario_cargo"] = $dados["cargo"];
                $_SESSION["tempoSessao"] = time() + 60;

                header("location: ../paginas/dashboard.php");
                exit();
            } else {
                header("location: ../login.php");
                exit();
            }
        } else {
            header("location: ../login.php");
            exit();
        }
    } catch(PDOException $erro) {
        echo("Entre em contato com o suporte!");
    }
} else {
    header("location: ../login.php");
    exit();
}