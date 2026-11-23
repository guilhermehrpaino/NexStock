<?php
/**
 * movimentacoes.php — Registro de Entrada e Saída de Estoque
 *
 * BACKEND:
 * session_start();
 * if (!isset($_SESSION['usuario_id'])) { header('Location: ../login.php'); exit; }
 *
 * // Buscar produtos ativos para o select:
 * $produtos = $pdo->query("SELECT id, nome, quantidade FROM produtos WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
 *
 * // Lógica de registro de movimentação (POST):
 * if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 *   // Valide: produto_id, tipo (entrada/saida), quantidade > 0
 *   // Para saída: verifique se quantidade <= estoque atual
 *   // UPDATE produtos SET quantidade = quantidade +/- ? WHERE id = ?
 *   // INSERT INTO movimentacoes (produto_id, tipo, quantidade, observacao, usuario_id, data_hora)
 *   //   VALUES (?, ?, ?, ?, ?, NOW())
 * }
 */

// Pré-seleciona o tipo se vier via GET (?tipo=entrada ou ?tipo=saida)
$tipoInformado = $_POST['tipo'] ?? $_GET['tipo'] ?? '';
$tipoPreSelecionado = in_array($tipoInformado, ['entrada', 'saida'], true)
  ? $tipoInformado
  : 'entrada';

$produtos   = [];   // substitua pela query acima
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

      <?php /* Alertas de sucesso/erro — preencha com PHP */ ?>

      <div style="display:grid; grid-template-columns:1fr 380px; gap:24px; align-items:start;">

        <!-- Formulário de movimentação -->
        <div class="card">
          <div class="card-header">
            <div class="card-title"><?= nexstock_icon('movement') ?> Registrar Movimentação</div>
          </div>
          <div class="card-body">

            <!--
              O PHP processa o POST deste formulário.
              Regras obrigatórias no backend:
              - tipo é obrigatório (entrada ou saida)
              - quantidade deve ser inteiro positivo
              - para saída: quantidade <= produto.quantidade atual
              - registrar em movimentacoes e atualizar produtos.quantidade
            -->
            <form method="POST" action="movimentacoes.php">

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

              <button type="submit" class="btn btn-primary" style="width:100%;">
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
