<?php
require_once("../php/_sessao.php");
require_once("../php/conexao.php");
require_once '../php/listarProdutos.php';


$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
$mensagens = [
  'sucesso' => 'Produto salvo com sucesso.',
  'excluido' => 'Produto excluído com sucesso.',
  'invalido' => 'Confira os dados do produto e tente novamente.',
  'sku-duplicado' => 'Este código SKU já está cadastrado.',
  'erro' => 'Não foi possível concluir a operação.',
];

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

      <?php if (isset($mensagens[$status])): ?>
        <div class="alert <?= in_array($status, ['sucesso', 'excluido'], true) ? 'alert-success' : 'alert-danger' ?>" role="status">
          <?= nexstock_icon(in_array($status, ['sucesso', 'excluido'], true) ? 'check' : 'alert') ?>
          <div><?= htmlspecialchars($mensagens[$status], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <?php if (empty($categorias)): ?>
        <div class="alert alert-warning" role="status">
          <?= nexstock_icon('alert') ?>
          <div>
            Cadastre uma categoria antes de criar um produto.
            <a href="categorias.php?novo=1#modalCategoria">Cadastrar categoria</a>
          </div>
        </div>
      <?php endif; ?>

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
                          <form method="POST" action="../php/excluirProduto.php">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <p>Desativar este produto? Ele continuará no histórico.</p>
                            <button type="submit" class="btn btn-sm btn-danger">Confirmar desativação</button>
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

        <div class="card-footer">
          <span class="text-muted fs-sm">
            Mostrando <?= count($produtos) ?> produto(s)
          </span>
        </div>
      </div>

    </main>
  </div>
</div>

<div class="modal-overlay" id="modalProduto" role="dialog" aria-modal="true">
  <a class="modal-backdrop" href="produtos.php" aria-label="Fechar"></a>
  <div class="modal">

    <div class="modal-header">
      <h2 class="modal-title"><?= nexstock_icon($produtoEdicao ? 'edit' : 'plus') ?> <?= $produtoEdicao ? 'Editar Produto' : 'Novo Produto' ?></h2>
      <a class="modal-close" href="produtos.php" aria-label="Fechar"><?= nexstock_icon('close') ?></a>
    </div>

    <form method="POST" action="<?= $produtoEdicao ? '../php/editarProduto.php' : '../php/cadastrarProduto.php' ?>">
      <?php if ($produtoEdicao): ?>
        <input type="hidden" name="id" value="<?= (int)$produtoEdicao['id'] ?>">
      <?php endif; ?>

      <div class="modal-body">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="nome">
              Nome do Produto <span class="required">*</span>
            </label>
            <input type="text" id="nome" name="nome" class="form-control"
                   placeholder="Ex.: Arroz 5kg" maxlength="180"
                   value="<?= htmlspecialchars($produtoEdicao['nome'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="codigo">Código (SKU)</label>
            <input type="text" id="codigo" name="codigo" class="form-control"
                   placeholder="Ex.: ARR001" maxlength="80"
                   value="<?= htmlspecialchars($produtoEdicao['codigo'] ?? '') ?>">
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
