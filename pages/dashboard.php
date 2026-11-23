<?php
/**
 * dashboard.php — Painel principal do sistema
 *
 * BACKEND: Antes de incluir a sidebar, inicie a sessão e proteja a página:
 *
 * session_start();
 * if (!isset($_SESSION['usuario_id'])) {
 *     header('Location: ../login.php');
 *     exit;
 * }
 *
 * Depois, busque os dados do banco com PDO e preencha as variáveis:
 * $totalProdutos, $produtosEstoqueBaixo, $totalMovimentacoes, $movimentacoesHoje
 * e o array $produtosBaixoEstoque para a tabela de alertas.
 */

// ── Dados de exemplo (substitua pelas suas queries PHP) ───
$totalProdutos        = 0;
$produtosEstoqueBaixo = 0;
$totalMovimentacoes   = 0;
$movimentacoesHoje    = 0;

// Exemplo de produtos com estoque baixo para a tabela de alertas
// Substitua por: $produtosBaixoEstoque = $pdo->query("SELECT ...")->fetchAll(PDO::FETCH_ASSOC);
$produtosBaixoEstoque = [];

// Exemplo de movimentações recentes
// Substitua por: $movimentacoesRecentes = $pdo->query("SELECT ...")->fetchAll(PDO::FETCH_ASSOC);
$movimentacoesRecentes = [];

// Define a página ativa para a sidebar
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — NexStock</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="app-wrapper">

  <?php include_once '../includes/sidebar.php'; ?>

  <div class="main-content">

    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <label class="btn-toggle-sidebar" for="sidebarToggle" aria-label="Abrir menu">
          <?= nexstock_icon('menu') ?>
        </label>
        <div>
          <div class="page-title">Dashboard</div>
          <div class="breadcrumb">
            <span>NexStock</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Visão Geral</span>
          </div>
        </div>
      </div>
      <div class="topbar-right">
        <span class="text-muted fs-sm">
          <?= date('d/m/Y') ?>
        </span>
      </div>
    </header>

    <!-- Conteúdo -->
    <main class="page-content">

      <!-- ── Cartões de estatísticas ─────────────────────── -->
      <div class="grid-stats">

        <div class="stat-card">
          <div class="stat-icon blue"><?= nexstock_icon('package', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($totalProdutos) ?></div>
            <div class="stat-label">Produtos Cadastrados</div>
            <div class="stat-trend up"><?= nexstock_icon('arrow-up') ?> Total no sistema</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green"><?= nexstock_icon('check', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($totalProdutos - $produtosEstoqueBaixo) ?></div>
            <div class="stat-label">Produtos em Dia</div>
            <div class="stat-trend up"><?= nexstock_icon('arrow-up') ?> Estoque normal</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon yellow"><?= nexstock_icon('alert', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($produtosEstoqueBaixo) ?></div>
            <div class="stat-label">Estoque Baixo</div>
            <div class="stat-trend warn"><?= nexstock_icon('arrow-down') ?> Requer atenção</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple"><?= nexstock_icon('movement', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($totalMovimentacoes) ?></div>
            <div class="stat-label">Movimentações</div>
            <div class="stat-trend up"><?= nexstock_icon('arrow-up') ?> <?= $movimentacoesHoje ?> hoje</div>
          </div>
        </div>

      </div>

      <!-- ── Linha de cards secundários ─────────────────── -->
      <div class="grid-2 mb-24">

        <!-- Alertas de estoque baixo -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title"><?= nexstock_icon('alert') ?> Alertas de Estoque</div>
              <div class="card-subtitle">Produtos abaixo do mínimo definido</div>
            </div>
            <a href="produtos.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
          </div>
          <div class="card-body" style="padding: 0;">

            <?php if (empty($produtosBaixoEstoque)): ?>
              <div class="empty-state" style="padding: 40px 20px;">
                <div class="empty-icon"><?= nexstock_icon('check', 'icon icon-xl') ?></div>
                <h3>Tudo certo!</h3>
                <p>Nenhum produto com estoque abaixo do mínimo.</p>
              </div>
            <?php else: ?>
              <div class="table-wrapper">
                <table>
                  <thead>
                    <tr>
                      <th>Produto</th>
                      <th>Categoria</th>
                      <th>Atual</th>
                      <th>Mínimo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($produtosBaixoEstoque as $produto): ?>
                    <tr>
                      <td class="fw-600"><?= htmlspecialchars($produto['nome']) ?></td>
                      <td><span class="badge badge-neutral"><?= htmlspecialchars($produto['categoria']) ?></span></td>
                      <td>
                        <span class="badge <?= $produto['quantidade'] == 0 ? 'badge-danger' : 'badge-warning' ?>">
                          <?= (int)$produto['quantidade'] ?>
                        </span>
                      </td>
                      <td class="text-muted"><?= (int)$produto['estoque_minimo'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

          </div>
        </div>

        <!-- Movimentações recentes -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title"><?= nexstock_icon('history') ?> Movimentações Recentes</div>
              <div class="card-subtitle">Últimas entradas e saídas</div>
            </div>
            <a href="historico.php" class="btn btn-sm btn-outline-primary">Ver histórico</a>
          </div>
          <div class="card-body" style="padding: 0;">

            <?php if (empty($movimentacoesRecentes)): ?>
              <div class="empty-state" style="padding: 40px 20px;">
                <div class="empty-icon"><?= nexstock_icon('inbox', 'icon icon-xl') ?></div>
                <h3>Sem movimentações</h3>
                <p>Nenhuma movimentação registrada ainda.</p>
              </div>
            <?php else: ?>
              <div class="table-wrapper">
                <table>
                  <thead>
                    <tr>
                      <th>Produto</th>
                      <th>Tipo</th>
                      <th>Qtd.</th>
                      <th>Data</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($movimentacoesRecentes as $mov): ?>
                    <tr>
                      <td class="fw-600"><?= htmlspecialchars($mov['produto']) ?></td>
                      <td>
                        <?php if ($mov['tipo'] === 'entrada'): ?>
                          <span class="badge badge-success"><?= nexstock_icon('arrow-up') ?> Entrada</span>
                        <?php else: ?>
                          <span class="badge badge-danger"><?= nexstock_icon('arrow-down') ?> Saída</span>
                        <?php endif; ?>
                      </td>
                      <td class="fw-700"><?= (int)$mov['quantidade'] ?></td>
                      <td class="text-muted fs-sm">
                        <?= date('d/m/Y H:i', strtotime($mov['data_hora'])) ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>

      <!-- ── Ações rápidas ───────────────────────────────── -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><?= nexstock_icon('bolt') ?> Ações Rápidas</div>
        </div>
        <div class="card-body">
          <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="produtos.php?novo=1#modalProduto" class="btn btn-primary">
              <?= nexstock_icon('plus') ?> Novo Produto
            </a>
            <a href="movimentacoes.php?tipo=entrada" class="btn btn-success">
              <?= nexstock_icon('arrow-up') ?> Registrar Entrada
            </a>
            <a href="movimentacoes.php?tipo=saida" class="btn btn-danger">
              <?= nexstock_icon('arrow-down') ?> Registrar Saída
            </a>
            <a href="categorias.php" class="btn btn-secondary">
              <?= nexstock_icon('category') ?> Gerenciar Categorias
            </a>
          </div>
        </div>
      </div>

    </main>
  </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->

</body>
</html>
