<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$autenticado = !empty($_SESSION['nomeUsuario'])
    && isset($_SESSION['tempoSessao'])
    && (int) $_SESSION['tempoSessao'] > time();

if ($autenticado) {
    header('Location: paginas/dashboard.php');
} else {
    header('Location: login.php');
}

exit;
