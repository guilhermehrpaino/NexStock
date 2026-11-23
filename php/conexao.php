<?php
$dns = "mysql:host=127.0.0.1;dbname=nexstock;charset=utf8";
$user = "root";
$pass = "";

try {
    $conexao = new PDO($dns, $user, $pass);
} catch (PDOException $erro) {
    echo("Entre em contato com o suporte!");
}