<?php
require_once("../php/_sessao.php");
require_once("../php/conexao.php");
require_once '../php/listarCategorias.php';


$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
$mensagens = [
  'sucesso' => 'Categoria salva com sucesso.',
  'excluida' => 'Categoria excluída com sucesso.',
  'invalida' => 'Informe um nome válido para a categoria.',
  'em-uso' => 'Não é possível excluir uma categoria que possui produtos.',
  'erro' => 'Não foi possível concluir a operação.',
];

$activePage = 'categorias';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categorias — NexStock</title>
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
          <div class="page-title">Categorias</div>
          <div class="breadcrumb">
            <span>NexStock</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Categorias</span>
          </div>
        </div>
      </div>
      <div class="topbar-right">
        <a class="btn btn-primary btn-sm" href="?novo=1#modalCategoria">
          <?= nexstock_icon('plus') ?> Nova Categoria
        </a>
      </div>
    </header>

    <main class="page-content">

      <?php if (isset($mensagens[$status])): ?>
        <div class="alert <?= in_array($status, ['sucesso', 'excluida'], true) ? 'alert-success' : 'alert-danger' ?>" role="status">
          <?= nexstock_icon(in_array($status, ['sucesso', 'excluida'], true) ? 'check' : 'alert') ?>
          <div><?= htmlspecialchars($mensagens[$status], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <!-- Layout em 2 colunas: tabela + painel lateral -->
      <div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

        <!-- Tabela de categorias -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title"><?= nexstock_icon('category') ?> Lista de Categorias</div>
              <div class="card-subtitle"><?= count($categorias) ?> categoria(s)</div>
            </div>
            <!-- Busca inline -->
            <form method="GET" action="categorias.php" class="search-bar" style="max-width:220px;">
              <span class="search-icon"><?= nexstock_icon('search') ?></span>
              <input type="search" name="busca" class="form-control"
                     placeholder="Buscar categoria..." value="<?= htmlspecialchars($busca) ?>">
            </form>
          </div>

          <div class="table-wrapper">
            <table id="tabelaCategorias">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nome</th>
                  <th>Descrição</th>
                  <th>Produtos</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($categorias)): ?>
                  <tr>
                    <td colspan="5">
                      <div class="empty-state">
                        <div class="empty-icon"><?= nexstock_icon('category', 'icon icon-xl') ?></div>
                        <h3><?= $busca !== '' ? 'Nenhuma categoria encontrada' : 'Nenhuma categoria cadastrada' ?></h3>
                        <p>Clique em "Nova Categoria" para começar.</p>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($categorias as $cat): ?>
                    <tr>
                      <td class="text-muted fs-sm"><?= (int)$cat['id'] ?></td>
                      <td class="fw-600"><?= htmlspecialchars($cat['nome']) ?></td>
                      <td class="text-muted">
                        <?= htmlspecialchars($cat['descricao'] ?? '—') ?>
                      </td>
                      <td>
                        <span class="badge badge-info">
                          <?= (int)($cat['total_produtos'] ?? 0) ?> produto(s)
                        </span>
                      </td>
                      <td>
                        <div class="td-actions">
                          <a class="btn btn-sm btn-secondary"
                             href="?editar=<?= (int)$cat['id'] ?>#modalCategoria"
                             title="Editar"><?= nexstock_icon('edit') ?></a>

                          <details class="delete-confirm">
                            <summary class="btn btn-sm btn-danger" title="Excluir"><?= nexstock_icon('trash') ?></summary>
                            <form method="POST" action="../php/excluirCategoria.php">
                              <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                              <p>Excluir esta categoria? Ela só pode ser excluída se não houver produtos associados.</p>
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
        </div><!-- /.card tabela -->

        <!-- Painel informativo -->
        <div style="display:flex; flex-direction:column; gap:16px;">

          <div class="card">
            <div class="card-body" style="text-align:center; padding:28px 20px;">
              <div class="icon-xl" style="margin:0 auto 12px;"><?= nexstock_icon('category', 'icon icon-xl') ?></div>
              <div class="stat-value"><?= $totalCategorias ?></div>
              <div class="stat-label">Total de Categorias</div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="card-title mb-8"><?= nexstock_icon('bulb') ?> Dica</div>
              <p class="fs-sm text-secondary" style="line-height:1.7;">
                Organize seus produtos em categorias para facilitar a busca e os relatórios.
                Cada produto deve pertencer a pelo menos uma categoria.
              </p>
            </div>
          </div>

        </div>

      </div><!-- /grid -->

    </main>
  </div>
</div>

<!-- Modal — Nova / Editar Categoria -->
<div class="modal-overlay" id="modalCategoria" role="dialog" aria-modal="true">
  <a class="modal-backdrop" href="categorias.php" aria-label="Fechar"></a>
  <div class="modal">

    <div class="modal-header">
      <h2 class="modal-title"><?= nexstock_icon($categoriaEdicao ? 'edit' : 'plus') ?> <?= $categoriaEdicao ? 'Editar Categoria' : 'Nova Categoria' ?></h2>
      <a class="modal-close" href="categorias.php" aria-label="Fechar"><?= nexstock_icon('close') ?></a>
    </div>

    <form method="POST" action="<?= $categoriaEdicao ? '../php/editarCategoria.php' : '../php/cadastrarCategoria.php' ?>">
      <?php if ($categoriaEdicao): ?>
        <input type="hidden" name="id" value="<?= (int)$categoriaEdicao['id'] ?>">
      <?php endif; ?>

      <div class="modal-body">

        <div class="form-group">
          <label class="form-label" for="catNome">
            Nome da Categoria <span class="required">*</span>
          </label>
          <input type="text" id="catNome" name="nome" class="form-control"
                 placeholder="Ex.: Alimentos, Eletrônicos..."
                 maxlength="120"
                 value="<?= htmlspecialchars($categoriaEdicao['nome'] ?? '') ?>" required>
        </div>

        <div class="form-group mb-0">
          <label class="form-label" for="catDescricao">Descrição</label>
          <textarea id="catDescricao" name="descricao" class="form-control"
                    placeholder="Descrição opcional..."><?= htmlspecialchars($categoriaEdicao['descricao'] ?? '') ?></textarea>
        </div>

      </div>

      <div class="modal-footer">
        <a class="btn btn-secondary" href="categorias.php">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <?= nexstock_icon('save') ?> <?= $categoriaEdicao ? 'Atualizar' : 'Salvar' ?>
        </button>
      </div>

    </form>
  </div>
</div>

</body>
</html>
