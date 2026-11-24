<?php
/**
 * sidebar.php — Componente de menu lateral reutilizável
 *
 * Como incluir:  <?php include_once '../includes/sidebar.php'; ?>
 * Marcar ativo: defina $activePage antes de incluir:
 *               $activePage = 'dashboard'; // ou 'produtos', 'categorias', etc.
 */

// Garante que $activePage existe e evita notice
$activePage = $activePage ?? '';

$userName = $_SESSION['usuario_nome'] ?? $_SESSION['nomeUsuario'] ?? 'Usuário';
$userRole = $_SESSION['usuario_cargo'] ?? 'Usuário';

// Gera as iniciais do avatar a partir do nome
$initials = '';
foreach (explode(' ', $userName) as $word) {
    $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    if (strlen($initials) >= 2) break;
}

// Define o prefixo de caminho relativo conforme a localização do arquivo chamador
// Páginas em /paginas/ precisam de '../', na raiz não precisam
$prefix = (strpos($_SERVER['PHP_SELF'], '/paginas/') !== false) ? '../' : '';
?>
<?php include_once __DIR__ . '/icons.php'; ?>

<?= nexstock_icons(); ?>

<input type="checkbox" class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">

<!-- ── Overlay (mobile) ──────────────────────────────────── -->
<label class="sidebar-overlay" for="sidebarToggle" aria-label="Fechar menu"></label>

<!-- ── Sidebar ───────────────────────────────────────────── -->
<aside class="sidebar" id="sidebar">

  <!-- Logo -->
  <div class="sidebar-logo">
    <div class="logo-icon"><?= nexstock_icon('package', 'icon icon-lg') ?></div>
    <div class="logo-text">Nex<span>Stock</span></div>
  </div>

  <!-- Navegação principal -->
  <nav class="sidebar-nav">

    <div class="sidebar-section">
      <p class="sidebar-section-label">Principal</p>

      <a href="<?= $prefix ?>paginas/dashboard.php"
         class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>"
         data-page="dashboard">
        <span class="nav-icon"><?= nexstock_icon('home') ?></span>
        Dashboard
      </a>
    </div>

    <div class="sidebar-section">
      <p class="sidebar-section-label">Estoque</p>

      <a href="<?= $prefix ?>paginas/produtos.php"
         class="nav-item <?= $activePage === 'produtos' ? 'active' : '' ?>"
         data-page="produtos">
        <span class="nav-icon"><?= nexstock_icon('products') ?></span>
        Produtos
      </a>

      <a href="<?= $prefix ?>paginas/categorias.php"
         class="nav-item <?= $activePage === 'categorias' ? 'active' : '' ?>"
         data-page="categorias">
        <span class="nav-icon"><?= nexstock_icon('category') ?></span>
        Categorias
      </a>

      <a href="<?= $prefix ?>paginas/movimentacoes.php"
         class="nav-item <?= $activePage === 'movimentacoes' ? 'active' : '' ?>"
         data-page="movimentacoes">
        <span class="nav-icon"><?= nexstock_icon('movement') ?></span>
        Movimentações
      </a>

      <a href="<?= $prefix ?>paginas/historico.php"
         class="nav-item <?= $activePage === 'historico' ? 'active' : '' ?>"
         data-page="historico">
        <span class="nav-icon"><?= nexstock_icon('history') ?></span>
        Histórico
      </a>
    </div>

    <div class="sidebar-section">
      <p class="sidebar-section-label">Administração</p>

      <a href="<?= $prefix ?>paginas/usuarios.php"
         class="nav-item <?= $activePage === 'usuarios' ? 'active' : '' ?>"
         data-page="usuarios">
        <span class="nav-icon"><?= nexstock_icon('users') ?></span>
        Usuários
      </a>
    </div>

  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($userName) ?></div>
        <div class="user-role"><?= htmlspecialchars($userRole) ?></div>
      </div>
      <a href="<?= $prefix ?>logout.php" class="btn-logout" title="Sair"><?= nexstock_icon('logout') ?></a>
    </div>
  </div>

</aside>
