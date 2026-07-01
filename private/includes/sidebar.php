<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_url = $_SERVER['REQUEST_URI'];
$nome   = $_SESSION['nome']   ?? ($_SESSION['utilizador'] ?? 'Utilizador');
$perfil = $_SESSION['perfil'] ?? '';

// Helpers
$isAdmin  = ($perfil === 'Administrador');
$isTec    = ($perfil === 'Técnico');

function sidebarLink(string $href, string $label, string $matchPath, string $icon): void {
    $active = (strpos($_SERVER['PHP_SELF'], $matchPath) !== false) ? 'active' : '';
    echo '<li class="nav-item">
        <a href="' . $href . '" class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 ' . $active . '">
            ' . $icon . '
            <span class="fw-medium">' . $label . '</span>
        </a>
    </li>';
}

$iconDashboard = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></svg>';
$iconEquip     = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 2v2"></path><path d="M5 2v2"></path><path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"></path><path d="M8 15a6 6 0 0 0 12 0v-3"></path><circle cx="20" cy="10" r="2"></circle></svg>';
$iconLoc       = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>';
$iconForn      = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg>';
$iconDoc       = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>';
$iconGar       = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>';
$iconContent   = '<i class="fa-regular fa-pen-to-square" style="width:20px; text-align:center;"></i>';
?>

<!-- SIDEBAR -->
<aside class="sidebar-backend d-none d-md-flex flex-column flex-shrink-0 h-100" style="width: 256px;">

    <div class="p-4 sidebar-border-light border-bottom">
        <div class="d-flex align-items-center gap-2">
            <img src="/projeto_SIBDAS/assets/img/logotipo.png" alt="MedStock Logo" style="height: 55px; width: auto;">
        </div>
    </div>

    <nav class="flex-grow-1 overflow-auto sidebar-scroll py-4 px-3">
        <ul class="nav flex-column gap-1">

            <?php /* Dashboard — todos os perfis */ ?>
            <?php sidebarLink('/projeto_SIBDAS/private/views/dashboard/dashboard.php', 'Dashboard', '/dashboard/', $iconDashboard); ?>

            <?php /* Equipamentos — todos os perfis */ ?>
            <?php sidebarLink('/projeto_SIBDAS/private/views/equipamentos/lista_equi.php', 'Equipamentos', '/equipamentos/', $iconEquip); ?>

            <?php /* Localizações — todos os perfis */ ?>
            <?php sidebarLink('/projeto_SIBDAS/private/views/localizacoes/lista_loc.php', 'Localizações', '/localizacoes/', $iconLoc); ?>

            <?php /* Fornecedores — apenas Administrador */ ?>
            <?php if ($isAdmin): ?>
                <?php sidebarLink('/projeto_SIBDAS/private/views/fornecedores/lista_fornecedores.php', 'Fornecedores', '/fornecedores/', $iconForn); ?>
            <?php endif; ?>

            <?php /* Documentação — todos os perfis */ ?>
            <?php sidebarLink('/projeto_SIBDAS/private/views/documentacao/lista_docs.php', 'Documentação', '/documentacao/', $iconDoc); ?>

            <?php /* Garantias — todos os perfis */ ?>
            <?php sidebarLink('/projeto_SIBDAS/private/views/garantias/lista_garantias.php', 'Garantias', '/garantias/', $iconGar); ?>

            <?php /* Conteúdo do site — apenas Administrador */ ?>
            <?php if ($isAdmin): ?>
                <?php sidebarLink('/projeto_SIBDAS/private/views/conteudo/conteudo.php', 'Conteúdo', '/conteudo/', $iconContent); ?>
            <?php endif; ?>

        </ul>
    </nav>

    <div class="p-4 sidebar-border-light border-top">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold bg-brand-subtle text-brand"
                style="width: 40px; height: 40px; font-size: 1.1rem;">
                <?= htmlspecialchars(mb_substr($perfil, 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <p class="mb-0 fw-semibold small text-white text-truncate"><?= htmlspecialchars($perfil) ?></p>
                <p class="mb-0 small text-white-50 text-truncate"><?= htmlspecialchars($_SESSION['utilizador'] ?? '') ?></p>
            </div>
        </div>
        <a href="/projeto_SIBDAS/login/logout.php" class="btn btn-outline-light btn-sm w-100 rounded-3">
            <i class="fa-solid fa-sign-out-alt me-1"></i> Terminar Sessão
        </a>
    </div>
</aside>