<?php
/**
 * produtos.php — Gestão de Produtos
 *
 * BACKEND: Proteja a página e busque os dados:
 *
 * session_start();
 * if (!isset($_SESSION['usuario_id'])) {
 *     header('Location: ../login.php');
 *     exit;
 * }
 *
 * // Buscar produtos com join de categoria:
 * $stmt = $pdo->query("
 *     SELECT p.*, c.nome AS categoria_nome
 *     FROM produtos p
 *     LEFT JOIN categorias c ON p.categoria_id = c.id
 *     ORDER BY p.nome ASC
 * ");
 * $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 *
 * // Buscar categorias para o select do formulário:
 * $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
 */

// ── Dados de exemplo (substitua pelas suas queries) ───────
$produtos   = [];   // array de produtos do banco
$categorias = [];   // array de categorias do banco
$produtoEdicao = null;
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
$busca = isset($_GET['busca']) && is_string($_GET['busca'])
  ? trim($_GET['busca'])
  : '';
$categoriaFiltro = isset($_GET['categoria']) && is_scalar($_GET['categoria'])
  ? (string)$_GET['categoria']
  : '';
$estoqueInformado = $_GET['estoque'] ?? '';
$estoqueFiltro = in_array($estoqueInformado, ['normal', 'baixo', 'zero'], true)
  ? $estoqueInformado
  : '';

foreach ($produtos as $produto) {
  if ($idEdicao && (int)$produto['id'] === $idEdicao) {
    $produtoEdicao = $produto;
    break;
  }
}

$produtos = array_values(array_filter($produtos, function ($produto) use ($busca, $categoriaFiltro, $estoqueFiltro) {
  $correspondeBusca = $busca === '' || stripos(
    ($produto['nome'] ?? '') . ' ' . ($produto['codigo'] ?? ''),
    $busca
  ) !== false;
  $correspondeCategoria = $categoriaFiltro === ''
    || (string)($produto['categoria_id'] ?? '') === $categoriaFiltro;
  $quantidade = (int)($produto['quantidade'] ?? 0);
  $minimo = (int)($produto['estoque_minimo'] ?? 0);
  $correspondeEstoque = $estoqueFiltro === ''
    || ($estoqueFiltro === 'zero' && $quantidade === 0)
    || ($estoqueFiltro === 'baixo' && $quantidade > 0 && $quantidade <= $minimo)
    || ($estoqueFiltro === 'normal' && $quantidade > $minimo);

  return $correspondeBusca && $correspondeCategoria && $correspondeEstoque;
}));

$activePage = 'produtos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produtos — NexStock</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="app-wrapper">

  <?php include_once '../includes/sidebar.php'; ?>

  <div class="main-content">

    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <label class="btn-toggle-sidebar" for="sidebarToggle" aria-label="Abrir menu"><?= nexstock_icon('menu') ?></label>
        <div>
          <div class="page-title">Produtos</div>
          <div class="breadcrumb">
            <span>NexStock</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Produtos</span>
          </div>
        </div>
      </div>
      <div class="topbar-right">
        <a class="btn btn-primary btn-sm" href="?novo=1#modalProduto">
          <?= nexstock_icon('plus') ?> Novo Produto
        </a>
      </div>
    </header>

    <main class="page-content">

      <!-- ── Feedback de operação (PHP preenche) ─────────── -->
      <?php
      /*
       * Exiba alertas conforme o resultado da operação do seu PHP:
       *
       * if (!empty($sucesso)) {
       *   echo '<div class="alert alert-success"><span class="alert-icon"><span data-icon="check"></span></span><span>' . htmlspecialchars($sucesso) . '</span></div>';
       * }
       * if (!empty($erro)) {
       *   echo '<div class="alert alert-danger"><span class="alert-icon"><span data-icon="x-circle"></span></span><span>' . htmlspecialchars($erro) . '</span></div>';
       * }
       */
      ?>

      <!-- ── Filtros e busca ─────────────────────────────── -->
      <div class="card mb-16">
        <div class="card-body" style="padding: 14px 20px;">
          <form method="GET" action="produtos.php" class="filters-bar">

            <div class="search-bar" style="flex:1; min-width:200px;">
              <span class="search-icon"><?= nexstock_icon('search') ?></span>
              <input
                type="search"
                name="busca"
                class="form-control"
                placeholder="Buscar por nome ou código..."
                value="<?= htmlspecialchars($busca) ?>"
              >
            </div>

            <!-- Filtro por categoria -->
            <select class="form-control" name="categoria" style="max-width:200px;">
              <option value="">Todas as categorias</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $categoriaFiltro === (string)$cat['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <!-- Filtro por status de estoque -->
            <select class="form-control" name="estoque" style="max-width:180px;">
              <option value="">Todos os estoques</option>
              <option value="normal" <?= $estoqueFiltro === 'normal' ? 'selected' : '' ?>>Estoque normal</option>
              <option value="baixo" <?= $estoqueFiltro === 'baixo' ? 'selected' : '' ?>>Estoque baixo</option>
              <option value="zero" <?= $estoqueFiltro === 'zero' ? 'selected' : '' ?>>Sem estoque</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
          </form>
        </div>
      </div>

      <!-- ── Tabela de produtos ──────────────────────────── -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= nexstock_icon('products') ?> Lista de Produtos</div>
            <div class="card-subtitle">
              <?= count($produtos) ?> produto(s) encontrado(s)
            </div>
          </div>
        </div>

        <div class="table-wrapper">
          <table id="tabelaProdutos">
            <thead>
              <tr>
                <th>#</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Mín.</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>

              <?php if (empty($produtos)): ?>
                <tr>
                  <td colspan="8">
                    <div class="empty-state">
                      <div class="empty-icon"><?= nexstock_icon('package', 'icon icon-xl') ?></div>
                      <h3><?= $busca !== '' || $categoriaFiltro !== '' || $estoqueFiltro !== '' ? 'Nenhum produto encontrado' : 'Nenhum produto cadastrado' ?></h3>
                      <p>Clique em "Novo Produto" para começar.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($produtos as $p): ?>
                  <?php
                    // Lógica de status de estoque
                    if ((int)$p['quantidade'] === 0) {
                      $statusClass = 'badge-danger';
                      $statusTxt   = 'Sem estoque';
                    } elseif ((int)$p['quantidade'] <= (int)$p['estoque_minimo']) {
                      $statusClass = 'badge-warning';
                      $statusTxt   = 'Baixo';
                    } else {
                      $statusClass = 'badge-success';
                      $statusTxt   = 'Normal';
                    }
                  ?>
                  <tr>
                    <td class="text-muted fs-sm"><?= (int)$p['id'] ?></td>
                    <td>
                      <div class="fw-600"><?= htmlspecialchars($p['nome']) ?></div>
                      <?php if (!empty($p['codigo'])): ?>
                        <div class="fs-xs text-muted"><?= htmlspecialchars($p['codigo']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge badge-neutral">
                        <?= htmlspecialchars($p['categoria_nome'] ?? '—') ?>
                      </span>
                    </td>
                    <td class="fw-600">
                      R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?>
                    </td>
                    <td>
                      <!-- Barra visual de estoque -->
                      <?php
                        $pct = $p['estoque_minimo'] > 0
                          ? min(100, round(($p['quantidade'] / ($p['estoque_minimo'] * 2)) * 100))
                          : 100;
                        $barClass = (int)$p['quantidade'] === 0 ? 'zero'
                          : ((int)$p['quantidade'] <= (int)$p['estoque_minimo'] ? 'low' : 'ok');
                      ?>
                      <div class="stock-bar">
                        <div class="stock-progress">
                          <div class="stock-progress-fill <?= $barClass ?>"
                               style="width: <?= $pct ?>%"></div>
                        </div>
                        <span class="stock-qty <?= $barClass ?>"><?= (int)$p['quantidade'] ?></span>
                      </div>
                    </td>
                    <td class="text-muted"><?= (int)$p['estoque_minimo'] ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= $statusTxt ?></span></td>
                    <td>
                      <div class="td-actions">
                        <a class="btn btn-sm btn-secondary"
                           href="?editar=<?= (int)$p['id'] ?>#modalProduto"
                           title="Editar"><?= nexstock_icon('edit') ?></a>

                        <details class="delete-confirm">
                          <summary class="btn btn-sm btn-danger" title="Excluir"><?= nexstock_icon('trash') ?></summary>
                          <form method="POST" action="produtos.php">
                            <input type="hidden" name="action" value="excluir">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <p>Confirma a exclusão deste produto?</p>
                            <button type="submit" class="btn btn-sm btn-danger">Confirmar exclusão</button>
                          </form>
                        </details>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>

            </tbody>
          </table>
        </div>

        <!-- Paginação — implemente com PHP -->
        <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center;">
          <span class="text-muted fs-sm">
            Mostrando <?= count($produtos) ?> produto(s)
          </span>
          <div class="pagination" id="paginacao">
            <!-- PHP gera os links de paginação aqui -->
          </div>
        </div>
      </div>

    </main>
  </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->

<!-- ═══════════════════════════════════════════════════════
     Modal — Novo / Editar Produto
     O mesmo modal serve para cadastro e edição.
     O PHP carrega os dados do produto selecionado via GET.
     ═══════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalProduto" role="dialog" aria-modal="true">
  <a class="modal-backdrop" href="produtos.php" aria-label="Fechar"></a>
  <div class="modal">

    <div class="modal-header">
      <h2 class="modal-title"><?= nexstock_icon($produtoEdicao ? 'edit' : 'plus') ?> <?= $produtoEdicao ? 'Editar Produto' : 'Novo Produto' ?></h2>
      <a class="modal-close" href="produtos.php" aria-label="Fechar"><?= nexstock_icon('close') ?></a>
    </div>

    <!--
      ACTION: aponta para produtos.php — você trata o POST no backend.
      O campo hidden "action" indica se é "criar" ou "editar".
      Os valores de edição são renderizados pelo PHP.
    -->
    <form method="POST" action="produtos.php">
      <input type="hidden" name="action" value="<?= $produtoEdicao ? 'editar' : 'criar' ?>">
      <input type="hidden" name="id" value="<?= (int)($produtoEdicao['id'] ?? 0) ?>">

      <div class="modal-body">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="nome">
              Nome do Produto <span class="required">*</span>
            </label>
            <input type="text" id="nome" name="nome" class="form-control"
                   placeholder="Ex.: Arroz 5kg" value="<?= htmlspecialchars($produtoEdicao['nome'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="codigo">Código (SKU)</label>
            <input type="text" id="codigo" name="codigo" class="form-control"
                   placeholder="Ex.: ARR001" value="<?= htmlspecialchars($produtoEdicao['codigo'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="categoria_id">
            Categoria <span class="required">*</span>
          </label>
          <select id="categoria_id" name="categoria_id" class="form-control" required>
            <option value="">Selecione uma categoria...</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>" <?= (string)($produtoEdicao['categoria_id'] ?? '') === (string)$cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="preco">
              Preço (R$) <span class="required">*</span>
            </label>
            <input type="number" id="preco" name="preco" class="form-control"
                   placeholder="0,00" step="0.01" min="0" value="<?= htmlspecialchars((string)($produtoEdicao['preco'] ?? '')) ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="estoque_minimo">
              Estoque Mínimo <span class="required">*</span>
            </label>
            <input type="number" id="estoque_minimo" name="estoque_minimo"
                   class="form-control" placeholder="Ex.: 10" min="0" value="<?= htmlspecialchars((string)($produtoEdicao['estoque_minimo'] ?? '')) ?>" required>
            <span class="form-hint">Quantidade mínima para alerta de estoque baixo.</span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao" class="form-control"
                    placeholder="Descrição opcional do produto..."><?= htmlspecialchars($produtoEdicao['descricao'] ?? '') ?></textarea>
        </div>

      </div><!-- /.modal-body -->

      <div class="modal-footer">
        <a class="btn btn-secondary" href="produtos.php">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <?= nexstock_icon('save') ?> <?= $produtoEdicao ? 'Atualizar Produto' : 'Salvar Produto' ?>
        </button>
      </div>

    </form>
  </div>
</div>

</body>
</html>
