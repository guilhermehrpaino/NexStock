<!DOCTYPE html>
<?php include_once 'includes/icons.php'; ?>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — NexStock</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?= nexstock_icons(); ?>

<div class="login-page">
  <div class="login-card">

    <!-- Logo -->
    <div class="login-logo">
      <div class="login-logo-icon"><?= nexstock_icon('package', 'icon icon-lg') ?></div>
      <div class="login-logo-text">
        <h1>Nex<span>Stock</span></h1>
        <p>Sistema de Gestão de Estoque</p>
      </div>
    </div>

    <!-- Título -->
    <h2>Bem-vindo de volta!</h2>
    <p class="login-subtitle">Faça login para acessar o sistema.</p>

    <form  action="./php/validar-login.php" method="POST">

      <div class="form-group">
        <label class="form-label" for="usuario">
          Usuário <span class="required">*</span>
        </label>
        <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuário" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="senha">
          Senha <span class="required">*</span>
        </label>
        <input type="password" id="senha" name="senha" class="form-control" placeholder="••••••••" 
        autocomplete="current-password" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; margin-top:8px;">
        Entrar no sistema
      </button>

    </form>

  </div>
</div>

</body>
</html>
