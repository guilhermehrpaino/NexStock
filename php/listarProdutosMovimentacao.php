<?php
$comandoProdutos = $conexao->query(
    "SELECT id, nome, codigo, quantidade FROM produtos WHERE ativo = 1 ORDER BY nome ASC"
);
$produtos = $comandoProdutos->fetchAll();
