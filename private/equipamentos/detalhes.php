<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedStock — Equipamentos</title>

    <link rel="stylesheet" href="../../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../../assets/css/1231184.css">
</head>

<body class="d-flex vh-100 overflow-hidden bg-backend">

    <aside class="sidebar-backend d-none d-md-flex flex-column flex-shrink-0 h-100" style="width: 256px;">
        <div class="p-4 sidebar-border-light border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="../../assets/img/logotipo.png" alt="MedStock Logo" style="height: 55px; width: auto;">
            </div>
        </div>

        <nav class="flex-grow-1 overflow-auto sidebar-scroll py-4 px-3">
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="../dashboard.html" class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3">
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
                    <a href="lista_equi.html" class="nav-link sidebar-item active d-flex align-items-center gap-2 py-2 px-3">
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
                    <a href="../localizacoes/lista_loc.html"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3">
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
                    <a href="../fornecedores/lista.html"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3">
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
                    <a href="../documentacao/lista.html"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3">
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
                    <a href="../garantias/lista.html"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3">
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
                    <a href="../alertas/lista.html"
                        class="nav-link sidebar-item d-flex align-items-center gap-2 py-2 px-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path>
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                        <span class="fw-medium">Alertas</span>
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
            <a href="../../public/index.html"
                class="btn btn-outline-secondary w-100 text-start border-0 text-secondary d-flex align-items-center gap-2 fs-6 shadow-none hover-white-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Terminar Sessão
            </a>
        </div>
    </aside>

    <main class="flex-grow-1 overflow-auto p-4 p-md-5">
        <div class="d-flex flex-column gap-4" style="max-width: 1024px; margin: 0 auto;">

            <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <a href="lista_equi.html"
                        class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h1 class="h3 fw-bold text-dark mb-0 me-2">Monitor Multiparamétrico de Sinais Vitais</h1>
                            <span class="badge badge-soft-success">Ativo</span>
                            <span class="badge badge-soft-warning">Alta</span>
                        </div>
                        <p class="text-muted custom-monospace small mb-0">EQ-2024-001</p>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="editar.html" class="btn-action-custom">
                        <i class="fa-solid fa-pencil me-2"></i> Editar
                    </a>
                    <button class="btn-action-custom btn-action-danger">
                        <i class="fa-solid fa-trash-can me-2"></i> Remover
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="card dash-card h-100">
                        <div class="card-header border-0 bg-transparent pt-4 px-4 pb-3">
                            <h6 class="card-title-custom">Identificação</h6>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <div class="row row-cols-2 g-4">
                                <div class="col">
                                    <p class="detail-label">Categoria</p>
                                    <p class="detail-value">Monitorização</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Marca</p>
                                    <p class="detail-value">Philips</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Modelo</p>
                                    <p class="detail-value">IntelliVue MX700</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Número de Série</p>
                                    <p class="detail-value custom-monospace" style="font-size: 0.75rem;">SN-PH-2024-001
                                    </p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Fabricante</p>
                                    <p class="detail-value">Philips Healthcare</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Ano de Fabrico</p>
                                    <p class="detail-value">2023</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aquisição -->
                <div class="col-12 col-md-6">
                    <div class="card dash-card h-100">
                        <div class="card-header border-0 bg-transparent pt-4 px-4 pb-3">
                            <h6 class="card-title-custom">Aquisição</h6>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <div class="row row-cols-2 g-4">
                                <div class="col">
                                    <p class="detail-label">Tipo de Entrada</p>
                                    <p class="detail-value">Compra</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Data de Aquisição</p>
                                    <p class="detail-value">15/01/2024</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Custo de Aquisição</p>
                                    <p class="detail-value">18 500,00 €</p>
                                </div>
                                <div class="col">
                                    <p class="detail-label">Documentos</p>
                                    <p class="detail-value d-flex align-items-center gap-1"><i
                                            class="fa-solid fa-file-lines text-muted"></i> 3</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localização Atual -->
            <div class="card dash-card">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-map-pin text-muted" style="font-size: 1rem;"></i>
                    <h6 class="card-title-custom mb-0">Localização Atual</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row row-cols-2 row-cols-md-4 g-4">
                        <div class="col">
                            <p class="detail-label">Edifício</p>
                            <p class="detail-value d-flex align-items-center gap-1"><i
                                    class="fa-regular fa-building text-muted"></i> Edifício Principal</p>
                        </div>
                        <div class="col">
                            <p class="detail-label">Piso</p>
                            <p class="detail-value">1</p>
                        </div>
                        <div class="col">
                            <p class="detail-label">Serviço</p>
                            <p class="detail-value">UCI</p>
                        </div>
                        <div class="col">
                            <p class="detail-label">Sala</p>
                            <p class="detail-value">Unidade A</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fornecedores -->
            <div class="card dash-card">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-users text-muted" style="font-size: 1rem;"></i>
                    <h6 class="card-title-custom mb-0">Fornecedores (2)</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="d-flex flex-column gap-2">
                        <a href="#" class="text-decoration-none text-dark">
                            <div class="list-box d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="detail-value fw-medium">Philips Healthcare</p>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">Distribuidor-Fornecedor
                                        Comercial</p>
                                </div>
                                <div class="text-end d-none d-sm-block">
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">+351 210 000 002</p>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                        contacto@philips-saude.pt</p>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="text-decoration-none text-dark">
                            <div class="list-box d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="detail-value fw-medium">MedServ - Assistência Técnica</p>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">Empresa de Assistência
                                        Técnica</p>
                                </div>
                                <div class="text-end d-none d-sm-block">
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">+351 210 000 004</p>
                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">suporte@medserv.pt</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Garantias e Contratos -->
            <div class="card dash-card">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-shield-check text-muted" style="font-size: 1rem;"></i>
                    <h6 class="card-title-custom mb-0">Garantias e Contratos (1)</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="list-box list-box-light border-0 mb-3">
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            <div class="col">
                                <p class="detail-label">Início</p>
                                <p class="detail-value">15/01/2024</p>
                            </div>
                            <div class="col">
                                <p class="detail-label">Fim</p>
                                <p class="detail-value">15/01/2027</p>
                            </div>
                            <div class="col">
                                <p class="detail-label">Contrato Manutenção</p>
                                <p class="detail-value">Sim</p>
                            </div>
                            <div class="col">
                                <p class="detail-label">Tipo</p>
                                <p class="detail-value">Full Service</p>
                            </div>
                            <div class="col mt-2">
                                <p class="detail-label">Entidade Responsável</p>
                                <p class="detail-value">Philips Healthcare</p>
                            </div>
                            <div class="col mt-2">
                                <p class="detail-label">Periodicidade</p>
                                <p class="detail-value">Anual</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentação -->
            <div class="card dash-card">
                <div
                    class="card-header border-0 bg-transparent pt-4 px-4 pb-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-regular fa-file-lines text-muted" style="font-size: 1rem;"></i>
                        <h6 class="card-title-custom mb-0">Documentação (3)</h6>
                    </div>
                    <a href="../documentacao/lista.html" class="btn-action-custom py-1 px-3">Gerir documentos</a>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="d-flex flex-column gap-2">
                        <!-- Ficheiro Normal -->
                        <div class="list-box list-box-light d-flex justify-content-between align-items-center">
                            <div>
                                <p class="detail-value fw-medium">IntelliVue MX700 - Manual do Utilizador</p>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Manual de Utilizador —
                                    01/01/2023</p>
                            </div>
                            <div class="text-end">
                                <p class="text-muted custom-monospace small mb-0" style="font-size: 0.75rem;">
                                    intellivue_mx700_manual_pt.pdf</p>
                            </div>
                        </div>

                        <!-- Ficheiro Expirado -->
                        <div class="list-box d-flex justify-content-between align-items-center"
                            style="background-color: rgba(254, 242, 242, 0.5); border-color: rgba(254, 202, 202, 0.5);">
                            <div>
                                <p class="detail-value fw-medium">Certificado de Calibração - Monitor MX700</p>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Certificado de Calibração —
                                    01/06/2024</p>
                            </div>
                            <div class="text-end">
                                <p class="text-danger fw-medium small mb-0" style="font-size: 0.75rem;">Validade:
                                    01/06/2025 (Expirado)</p>
                                <p class="text-muted custom-monospace small mb-0" style="font-size: 0.75rem;">
                                    calib_monitor_mx700_2024.pdf</p>
                            </div>
                        </div>

                        <!-- Ficheiro Normal -->
                        <div class="list-box list-box-light d-flex justify-content-between align-items-center">
                            <div>
                                <p class="detail-value fw-medium">Contrato Full Service Philips 2024-2027</p>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Contrato de Manutenção —
                                    15/01/2024</p>
                            </div>
                            <div class="text-end">
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Validade: 15/01/2027</p>
                                <p class="text-muted custom-monospace small mb-0" style="font-size: 0.75rem;">
                                    contrato_philips_2024.pdf</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observações -->
            <div class="card dash-card">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-3">
                    <h6 class="card-title-custom">Observações</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <p class="detail-value text-muted" style="font-size: 0.875rem;">Monitor de UCI com 12 derivações ECG
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="d-flex gap-4 text-muted mt-2" style="font-size: 0.75rem;">
                <span>Criado: 16/04/2026</span>
                <span>Atualizado: 16/04/2026</span>
            </div>

        </div>
    </main>

    <script src="../../assets/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/1231184.js"></script>
</body>

</html>