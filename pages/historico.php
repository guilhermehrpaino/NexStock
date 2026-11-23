<?php
/**
 * historico.php — Histórico de Movimentações
 *
 * BACKEND:
 * session_start();
 * if (!isset($_SESSION['usuario_id'])) { header('Location: ../login.php'); exit; }
 *
 * // Buscar histórico com join de produto e usuário:
 * $stmt = $pdo->query("
 *     SELECT
 *         m.id,
 *         m.tipo,
 *         m.quantidade,
 *         m.observacao,
 *         m.data_hora,
 *         p.nome  AS produto_nome,
 *         p.codigo AS produto_codigo,
 *         u.nome  AS usuario_nome
 *     FROM movimentacoes m
 *     JOIN produtos  p ON m.produto_id  = p.id
 *     JOIN usuarios  u ON m.usuario_id  = u.id
 *     ORDER BY m.data_hora DESC
 *     LIMIT 100
 * ");
 * $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
 *
 * // Totais para os cards:
 * $totalEntradas = $pdo->query("SELECT COALESCE(SUM(quantidade),0) FROM movimentacoes WHERE tipo='entrada'")->fetchColumn();
 * $totalSaidas   = $pdo->query("SELECT COALESCE(SUM(quantidade),0) FROM movimentacoes WHERE tipo='saida'")->fetchColumn();
 * $totalMovs     = $pdo->query("SELECT COUNT(*) FROM movimentacoes")->fetchColumn();
 */

$movimentacoes = [];
$totalEntradas = 0;
$totalSaidas   = 0;
$totalMovs     = 0;
$busca = isset($_GET['busca']) && is_string($_GET['busca'])
  ? trim($_GET['busca'])
  : '';
$tipoFiltro = in_array($_GET['tipo'] ?? '', ['entrada', 'saida'], true)
  ? $_GET['tipo']
  : '';
$dataInicioInformada = $_GET['de'] ?? '';
$dataFimInformada = $_GET['ate'] ?? '';
$dataInicio = is_string($dataInicioInformada)
  && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInicioInformada)
  ? $_GET['de']
  : '';
$dataFim = is_string($dataFimInformada)
  && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFimInformada)
  ? $_GET['ate']
  : '';

$movimentacoes = array_values(array_filter($movimentacoes, function ($movimentacao) use ($busca, $tipoFiltro, $dataInicio, $dataFim) {
  $correspondeBusca = $busca === '' || stripos(
    ($movimentacao['produto_nome'] ?? '') . ' ' . ($movimentacao['produto_codigo'] ?? ''),
    $busca
  ) !== false;
  $correspondeTipo = $tipoFiltro === '' || ($movimentacao['tipo'] ?? '') === $tipoFiltro;
  $dataMovimentacao = substr((string)($movimentacao['data_hora'] ?? ''), 0, 10);
  $correspondeInicio = $dataInicio === '' || $dataMovimentacao >= $dataInicio;
  $correspondeFim = $dataFim === '' || $dataMovimentacao <= $dataFim;

  return $correspondeBusca && $correspondeTipo && $correspondeInicio && $correspondeFim;
}));

$activePage = 'historico';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico — NexStock</title>
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
          <div class="page-title">Histórico</div>
          <div class="breadcrumb">
            <span>NexStock</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Histórico de Movimentações</span>
          </div>
        </div>
      </div>
      <div class="topbar-right">
        <a href="movimentacoes.php" class="btn btn-primary btn-sm">
          <?= nexstock_icon('plus') ?> Nova Movimentação
        </a>
      </div>
    </header>

    <main class="page-content">

      <!-- ── Cards de resumo ────────────────────────────── -->
      <div class="grid-stats mb-24" style="grid-template-columns: repeat(3, 1fr);">

        <div class="stat-card">
          <div class="stat-icon purple"><?= nexstock_icon('chart', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($totalMovs) ?></div>
            <div class="stat-label">Total de Movimentações</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green"><?= nexstock_icon('arrow-up', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($totalEntradas) ?></div>
            <div class="stat-label">Unidades Entrada</div>
            <div class="stat-trend up"><?= nexstock_icon('arrow-up') ?> Soma total</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon red"><?= nexstock_icon('arrow-down', 'icon icon-xl') ?></div>
          <div class="stat-info">
            <div class="stat-value"><?= number_format($totalSaidas) ?></div>
            <div class="stat-label">Unidades Saída</div>
            <div class="stat-trend down"><?= nexstock_icon('arrow-down') ?> Soma total</div>
          </div>
        </div>

      </div>

      <!-- ── Filtros ─────────────────────────────────────── -->
      <div class="card mb-16">
        <div class="card-body" style="padding:14px 20px;">
          <form method="GET" action="historico.php" class="filters-bar">

            <div class="search-bar" style="flex:1; min-width:200px;">
              <span class="search-icon"><?= nexstock_icon('search') ?></span>
              <input type="search" name="busca" class="form-control"
                     placeholder="Buscar por produto..." value="<?= htmlspecialchars($busca) ?>">
            </div>

              <select class="form-control" name="tipo" style="max-width:160px;">
              <option value="">Todos os tipos</option>
                <option value="entrada" <?= $tipoFiltro === 'entrada' ? 'selected' : '' ?>>Entradas</option>
                <option value="saida" <?= $tipoFiltro === 'saida' ? 'selected' : '' ?>>Saídas</option>
            </select>

            <!-- Filtro por período (você pode usar no PHP via GET) -->
            <input type="date" class="form-control" name="de"
                   style="max-width:160px;" title="Data inicial" value="<?= htmlspecialchars($dataInicio) ?>">
            <input type="date" class="form-control" name="ate"
                   style="max-width:160px;" title="Data final" value="<?= htmlspecialchars($dataFim) ?>">

            <button type="submit" class="btn btn-secondary btn-sm">
              <?= nexstock_icon('filter') ?> Filtrar
            </button>

          </form>
        </div>
      </div>

      <!-- ── Tabela de histórico ────────────────────────── -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= nexstock_icon('history') ?> Registro de Movimentações</div>
            <div class="card-subtitle">
              <?= count($movimentacoes) ?> movimentação(ões) listada(s)
            </div>
          </div>
        </div>

        <div class="table-wrapper">
          <table id="tabelaHistorico">
            <thead>
              <tr>
                <th>#</th>
                <th>Data / Hora</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Usuário</th>
                <th>Observação</th>
              </tr>
            </thead>
            <tbody>

              <?php if (empty($movimentacoes)): ?>
                <tr>
                  <td colspan="7">
                    <div class="empty-state">
                      <div class="empty-icon"><?= nexstock_icon('inbox', 'icon icon-xl') ?></div>
                      <h3>Nenhuma movimentação encontrada</h3>
                      <p>As movimentações registradas aparecerão aqui.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($movimentacoes as $mov): ?>
                  <tr data-tipo="<?= htmlspecialchars($mov['tipo']) ?>">
                    <td class="text-muted fs-sm"><?= (int)$mov['id'] ?></td>
                    <td class="fs-sm" style="white-space:nowrap;">
                      <?= date('d/m/Y', strtotime($mov['data_hora'])) ?><br>
                      <span class="text-muted"><?= date('H:i', strtotime($mov['data_hora'])) ?></span>
                    </td>
                    <td>
                      <div class="fw-600"><?= htmlspecialchars($mov['produto_nome']) ?></div>
                      <?php if (!empty($mov['produto_codigo'])): ?>
                        <div class="fs-xs text-muted"><?= htmlspecialchars($mov['produto_codigo']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($mov['tipo'] === 'entrada'): ?>
                        <span class="badge badge-success"><?= nexstock_icon('arrow-up') ?> Entrada</span>
                      <?php else: ?>
                        <span class="badge badge-danger"><?= nexstock_icon('arrow-down') ?> Saída</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="fw-700 <?= $mov['tipo'] === 'entrada' ? 'text-success' : 'text-danger' ?>">
                        <?= $mov['tipo'] === 'entrada' ? '+' : '-' ?><?= (int)$mov['quantidade'] ?>
                      </span>
                    </td>
                    <td class="text-muted fs-sm">
                      <?= htmlspecialchars($mov['usuario_nome'] ?? '—') ?>
                    </td>
                    <td class="text-muted fs-sm">
                      <?= htmlspecialchars($mov['observacao'] ?? '—') ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>

            </tbody>
          </table>
        </div>

        <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center;">
          <span class="text-muted fs-sm">
            <?= count($movimentacoes) ?> registro(s) exibido(s)
          </span>
          <div class="pagination" id="paginacaoHistorico">
            <!-- PHP gera os links de paginação aqui -->
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

</body>
</html>
