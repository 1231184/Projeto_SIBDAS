<!-- SIDEBAR -->
    <aside class="sidebar-backend d-none d-md-flex flex-column flex-shrink-0 h-100" style="width: 256px;">

        <div class="p-4 sidebar-border-light border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="/projeto_SIBDAS/assets/img/logotipo.png" alt="MedStock Logo" style="height: 55px; width: auto;">
            </div>
        </div>

        <nav class="flex-grow-1 overflow-auto sidebar-scroll py-4 px-3">
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/dashboard/dashboard.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false) ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                            <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                        </svg>
                        <span class="fw-medium">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/equipamentos/lista_equi.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/equipamentos/') !== false) ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 2v2"></path>
                            <path d="M5 2v2"></path>
                            <path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"></path>
                            <path d="M8 15a6 6 0 0 0 12 0v-3"></path>
                            <circle cx="20" cy="10" r="2"></circle>
                        </svg>
                        <span class="fw-medium">Equipamentos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/localizacoes/lista_loc.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/localizacoes/') !== false) ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                            </path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span class="fw-medium">Localizações</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/fornecedores/lista_fornecedores.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/fornecedores/') !== false) ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                            <path d="M15 18H9"></path>
                            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14">
                            </path>
                            <circle cx="17" cy="18" r="2"></circle>
                            <circle cx="7" cy="18" r="2"></circle>
                        </svg>
                        <span class="fw-medium">Fornecedores</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/documentacao/lista_docs.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/documentacao/') !== false) ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                            <path d="M10 9H8"></path>
                            <path d="M16 13H8"></path>
                            <path d="M16 17H8"></path>
                        </svg>
                        <span class="fw-medium">Documentação</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/garantias/lista_garantias.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/garantias/') !== false) ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                            </path>
                            <path d="M12 8v4"></path>
                            <path d="M12 16h.01"></path>
                        </svg>
                        <span class="fw-medium">Garantias</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/projeto_SIBDAS/private/conteudo/conteudo.php"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3 <?php echo (strpos($_SERVER['PHP_SELF'], '/conteudo/') !== false) ? 'active' : ''; ?>">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <span class="fw-medium">
                            Conteúdo
                        </span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 sidebar-border-light border-top">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold bg-brand-subtle text-brand"
                    style="width: 40px; height: 40px; font-size: 1.1rem;">A</div>
                <div class="d-flex flex-column overflow-hidden">
                    <span class="text-white text-truncate fs-6 fw-semibold">Administrador</span>
                    <span class="text-secondary text-truncate" style="font-size: 0.85rem;">admin</span>
                </div>
            </div>
            <a href="/projeto_SIBDAS/public/index.php"
                class="btn btn-outline-secondary w-100 text-start border-0 text-secondary d-flex align-items-center gap-2 fs-6 shadow-none hover-white-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Terminar Sessão
            </a>
        </div>
    </aside>