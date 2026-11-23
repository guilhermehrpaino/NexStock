<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    empty($_SESSION["nomeUsuario"])
    || !isset($_SESSION["tempoSessao"])
    || (int) $_SESSION["tempoSessao"] <= time()
) {
    header("Location: ../login.php");
    exit();
}

$_SESSION["tempoSessao"] = time() + 60;