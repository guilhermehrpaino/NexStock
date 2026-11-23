<?php
if(!isset($_SESSION)){
    session_start();
}

if(!isset($_SESSION["nomeUsuario"]) || (time() > $_SESSION["tempoSessao"])){

    header("location:./index.php");
    exit();
}

$_SESSION["tempoSessao"] = time() + 60;