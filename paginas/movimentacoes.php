<?php
require_once("../php/_sessao.php");
require_once("../php/conexao.php");
require_once '../php/listarProdutosMovimentacao.php';

$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
$mensagens = [
  'sucesso' => 'Movimentação registrada com sucesso.',
  'invalido' => 'Confira os dados da movimentação e tente novamente.',
  'estoque-insuficiente' => 'A quantidade de saída não pode ser maior que o estoque atual.',
  'produto-invalido' => 'O produto selecionado não está disponível.',
  'usuario-invalido' => 'Não foi possível identificar seu usuário. Faça login novamente.',
  'erro' => 'Não foi possível registrar a movimentação.',
];

$tipoInformado = $_GET['tipo'] ?? '';
$tipoPreSelecionado = in_array($tipoInformado, ['entrada', 'saida'], true)
  ? $tipoInformado
  : 'entrada';

$activePage = 'movimentacoes';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movimentações — NexStock</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="app-wrapper">

  <?php include_once '../includes/sidebar.php'; ?>

  <div class="main-content">

    <header class="topbar">
      <div class="topbar-left">
        <label class="btn-toggle-sidebar" for="sidebarToggle" aria-label="Abrir menu"><?= nexstock_icon('menu') ?></label>
        <div>
          <div class="page-title">Movimentações</div>
          <div class="breadcrumb">
            <span>NexStock</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Movimentações</span>
          </div>
        </div>
      </div>
    </header>

    <main class="page-content">

      <?php if (isset($mensagens[$status])): ?>
        <div class="alert <?= $status === 'sucesso' ? 'alert-success' : 'alert-danger' ?>" role="status">
          <?= nexstock_icon($status === 'sucesso' ? 'check' : 'alert') ?>
          <div><?= htmlspecialchars($mensagens[$status], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <?php if (empty($produtos)): ?>
        <div class="alert alert-warning" role="status">
          <?= nexstock_icon('alert') ?>
          <div>
            Cadastre um produto antes de registrar uma movimentação.
            <a href="produtos.php">Cadastrar produto</a>
          </div>
        </div>
      <?php endif; ?>

      <div style="display:grid; grid-template-columns:1fr 380px; gap:24px; align-items:start;">

        <!-- Formulário de movimentação -->
        <div class="card">
          <div class="card-header">
            <div class="card-title"><?= nexstock_icon('movement') ?> Registrar Movimentação</div>
          </div>
          <div class="card-body">

            <form method="POST" action="../php/registrarMovimentacao.php">

              <!-- Tipo de movimentação -->
              <div class="form-group">
                <label class="form-label">
                  Tipo de Movimentação <span class="required">*</span>
                </label>

                <div style="display:flex; gap:12px;">

                  <label class="tipo-btn">
                    <input type="radio" name="tipo" value="entrada" id="radioEntrada"
                           <?= $tipoPreSelecionado === 'entrada' ? 'checked' : '' ?> required>
                    <span><?= nexstock_icon('arrow-up', 'icon icon-lg') ?></span>
                    <strong>Entrada</strong>
                    <small>Adicionar ao estoque</small>
                  </label>

                  <label class="tipo-btn">
                    <input type="radio" name="tipo" value="saida" id="radioSaida"
                           <?= $tipoPreSelecionado === 'saida' ? 'checked' : '' ?>>
                    <span><?= nexstock_icon('arrow-down', 'icon icon-lg') ?></span>
                    <strong>Saída</strong>
                    <small>Retirar do estoque</small>
                  </label>

                </div>
              </div>

              <!-- Produto -->
              <div class="form-group">
                <label class="form-label" for="produto_id">
                  Produto <span class="required">*</span>
                </label>
                <select id="produto_id" name="produto_id" class="form-control" required>
                  <option value="">Selecione o produto...</option>
                  <?php foreach ($produtos as $p): ?>
                    <option
                      value="<?= (int)$p['id'] ?>"
                    >
                      <?= htmlspecialchars($p['nome']) ?>
                      (Estoque atual: <?= (int)$p['quantidade'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Quantidade -->
              <div class="form-group">
                <label class="form-label" for="quantidade">
                  Quantidade <span class="required">*</span>
                </label>
                <input
                  type="number"
                  id="quantidade"
                  name="quantidade"
                  class="form-control"
                  placeholder="Ex.: 10"
                  min="1"
                  step="1"
                  required
                >
                <span class="form-hint">O estoque atual aparece ao lado de cada produto. Saídas não podem exceder o saldo disponível.</span>
              </div>

              <!-- Observação -->
              <div class="form-group mb-0">
                <label class="form-label" for="observacao">Observação</label>
                <textarea id="observacao" name="observacao" class="form-control"
                          placeholder="Motivo, fornecedor, número de nota fiscal..."></textarea>
              </div>

              <div class="divider"></div>

              <button type="submit" class="btn btn-primary" style="width:100%;" <?= empty($produtos) ? 'disabled' : '' ?>>
                <?= nexstock_icon('check') ?> Registrar Movimentação
              </button>

            </form>

          </div><!-- /.card-body -->
        </div><!-- /.card formulário -->

        <!-- Painel lateral de orientações -->
        <div style="display:flex; flex-direction:column; gap:16px;">

          <!-- Resumo rápido -->
          <div class="card">
            <div class="card-header">
              <div class="card-title"><?= nexstock_icon('info') ?> Orientações</div>
            </div>
            <div class="card-body">
              <ul style="display:flex; flex-direction:column; gap:12px;">
                <li class="d-flex gap-8">
                  <span><?= nexstock_icon('arrow-up') ?></span>
                  <div>
                    <div class="fw-600 fs-sm">Entrada</div>
                    <div class="fs-xs text-muted">
                      Registre quando produtos chegam ao estoque
                      (compras, devoluções, ajustes positivos).
                    </div>
                  </div>
                </li>
                <div class="divider" style="margin:0;"></div>
                <li class="d-flex gap-8">
                  <span><?= nexstock_icon('arrow-down') ?></span>
                  <div>
                    <div class="fw-600 fs-sm">Saída</div>
                    <div class="fs-xs text-muted">
                      Registre quando produtos saem do estoque
                      (vendas, perdas, ajustes negativos).
                    </div>
                  </div>
                </li>
                <div class="divider" style="margin:0;"></div>
                <li class="d-flex gap-8">
                  <span><?= nexstock_icon('alert') ?></span>
                  <div>
                    <div class="fw-600 fs-sm">Atenção</div>
                    <div class="fs-xs text-muted">
                      Saídas maiores que o estoque disponível
                      serão bloqueadas automaticamente.
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Link para o histórico -->
          <div class="card">
            <div class="card-body" style="text-align:center; padding:24px;">
              <div style="margin-bottom:8px;"><?= nexstock_icon('history', 'icon icon-xl') ?></div>
              <div class="fw-600 mb-8">Histórico completo</div>
              <p class="fs-sm text-muted mb-16">
                Veja todas as movimentações já realizadas.
              </p>
              <a href="historico.php" class="btn btn-outline-primary btn-sm">
                Ver Histórico
              </a>
            </div>
          </div>

        </div><!-- /painel lateral -->

      </div><!-- /grid -->

    </main>
  </div>
</div>

<style>
/* Botões de seleção de tipo */
.tipo-btn {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 16px 12px;
  border: 2px solid var(--border);
  border-radius: var(--radius-lg);
  cursor: pointer;
  text-align: center;
  transition: border-color var(--transition), background var(--transition);
}
.tipo-btn input[type="radio"] { margin: 0 0 4px; }
.tipo-btn:hover { border-color: var(--primary-light); background: var(--primary-bg); }
.tipo-btn small { font-size: 0.72rem; color: var(--text-muted); }
</style>

</body>
</html>
