<?php
require_once '../php/_sessao.php';

$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
$mensagens = [
    'sucesso' => 'Usuário cadastrado com sucesso.',
    'duplicado' => 'Este nome de usuário já está em uso.',
    'invalido' => 'Confira os dados: a senha deve ter ao menos 8 caracteres e as senhas devem coincidir.',
    'erro' => 'Não foi possível cadastrar o usuário. Tente novamente.',
];
$activePage = 'usuarios';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Novo Usuário — NexStock</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="app-wrapper">

  <?php include_once '../includes/sidebar.php'; ?>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <label class="btn-toggle-sidebar" for="sidebarToggle" aria-label="Abrir menu">
          <?= nexstock_icon('menu') ?>
        </label>
        <div>
          <div class="page-title">Usuários</div>
          <div class="breadcrumb">
            <span>NexStock</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Novo usuário</span>
          </div>
        </div>
      </div>
    </header>

    <main class="page-content">
      <div style="max-width:900px; margin:0 auto;">
        <div class="alert alert-info" role="note">
          <?= nexstock_icon('info') ?>
          <div>Somente administradores podem cadastrar usuários. As senhas são armazenadas com hash.</div>
        </div>

        <?php if (isset($mensagens[$status])): ?>
          <div class="alert <?= $status === 'sucesso' ? 'alert-success' : 'alert-danger' ?>" role="status">
            <?= nexstock_icon($status === 'sucesso' ? 'check' : 'alert') ?>
            <div><?= htmlspecialchars($mensagens[$status], ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        <?php endif; ?>

        <section class="card" aria-labelledby="formTitle">
          <div class="card-header">
            <div>
              <h1 class="card-title" id="formTitle">
                <?= nexstock_icon('users') ?> Cadastrar usuário
              </h1>
              <p class="card-subtitle">Preencha os dados de acesso do novo usuário.</p>
            </div>
          </div>

          <div class="card-body">
            <form action="../php/cadastrarUsuario.php" method="post" id="formUsuario">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="nome">
                    Nome completo <span class="required">*</span>
                  </label>
                  <input
                    class="form-control"
                    id="nome"
                    name="nome"
                    type="text"
                    autocomplete="name"
                    maxlength="120"
                    placeholder="Ex.: Maria da Silva"
                    required
                  >
                </div>

                <div class="form-group">
                  <label class="form-label" for="usuario">
                    Nome de usuário <span class="required">*</span>
                  </label>
                  <input
                    class="form-control"
                    id="usuario"
                    name="usuario"
                    type="text"
                    autocomplete="username"
                    maxlength="80"
                    placeholder="Ex.: maria.silva"
                    required
                  >
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="cargo">
                    Perfil de acesso <span class="required">*</span>
                  </label>
                  <select class="form-control" id="cargo" name="cargo" required>
                    <option value="Operador">Operador</option>
                    <option value="Administrador">Administrador</option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label" for="senha">
                    Senha <span class="required">*</span>
                  </label>
                  <input
                    class="form-control"
                    id="senha"
                    name="senha"
                    type="password"
                    autocomplete="new-password"
                    minlength="8"
                    placeholder="Mínimo de 8 caracteres"
                    required
                  >
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="confirmarSenha">
                  Confirmar senha <span class="required">*</span>
                </label>
                <input
                  class="form-control"
                  id="confirmarSenha"
                  name="confirmar_senha"
                  type="password"
                  autocomplete="new-password"
                  minlength="8"
                  placeholder="Digite a senha novamente"
                  required
                >
              </div>

              <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
                <a class="btn btn-secondary" href="dashboard.php">Cancelar</a>
                <button class="btn btn-primary" id="btnCadastro" type="submit">
                  <?= nexstock_icon('check') ?> Cadastrar usuário
                </button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </main>
  </div>
</div>

<script>
  const formUsuario = document.getElementById('formUsuario');
  const senha = document.getElementById('senha');
  const confirmarSenha = document.getElementById('confirmarSenha');

  confirmarSenha.addEventListener('input', () => confirmarSenha.setCustomValidity(''));

  formUsuario.addEventListener('submit', (event) => {
    if (senha.value !== confirmarSenha.value) {
      event.preventDefault();
      confirmarSenha.setCustomValidity('As senhas não coincidem.');
      confirmarSenha.reportValidity();
    }
  });
</script>

</body>
</html>
