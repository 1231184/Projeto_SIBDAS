<?php include '../includes/header.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <!-- MAIN -->
    <main class="flex-grow-1 overflow-auto p-4 p-md-5">

        <header class="d-md-none d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-stethoscope fs-5 text-brand"></i>
                <h1 class="h5 fw-bold mb-0 text-dark">MedStock</h1>
            </div>
            <button class="btn btn-light border-0 shadow-sm"><i class="fa-solid fa-bars"></i></button>
        </header>

        <div id="view-lista">
            <div class="mb-4">
                <h1 class="h3 fw-bold text-dark mb-1">Garantias e Contratos</h1>
                <p class="text-muted small mb-0">Visão global das coberturas legais e de manutenção do parque tecnológico.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <div class="position-relative flex-grow-1" style="max-width: 350px;">
                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" class="form-control ps-5 shadow-sm border-0" placeholder="Pesquisar equipamento ou contrato..." style="border-radius: 9px; padding-top: 10px; padding-bottom: 10px;">
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-filter active">Todos</button>
                    <button class="btn-filter">Garantias</button>
                    <button class="btn-filter">Contratos</button>
                    <button class="btn-filter text-warning fw-bold border-warning bg-warning bg-opacity-10">A Expirar</button>
                    <button class="btn-filter text-danger fw-bold border-danger bg-danger bg-opacity-10">Expirados</button>
                </div>
            </div>

            <div class="card dash-card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipamento</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tipo (Garantia / Contrato)</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Entidade Responsável</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Fim de Validade</th>
                                <th class="px-4 py-3 text-muted text-uppercase text-end" style="font-size: 0.7rem; letter-spacing: 0.5px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="text-brand fw-bold small">EQ-2024-001</div>
                                    <div class="text-dark fw-medium" style="font-size: 0.80rem;">Monitor Multiparamétrico</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-shield-check text-success"></i>
                                        <span class="text-dark fw-medium small">Garantia Legal</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-muted small">Philips Healthcare PT</td>
                                <td class="px-3 py-3 text-dark fw-bold small">15/01/2027</td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">Ativa</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="px-4 py-3">
                                    <div class="text-brand fw-bold small">EQ-0042</div>
                                    <div class="text-dark fw-medium" style="font-size: 0.80rem;">Ventilador Pulmonar</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <div class="text-dark fw-medium small lh-1">Contrato: Full-Service</div>
                                            <span class="text-muted" style="font-size: 0.65rem;">Ref: CNT-2024-883</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-muted small">Dräger Portugal</td>
                                <td class="px-3 py-3 text-warning fw-bold small">30/06/2024</td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i>A Expirar</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    
<?php include '../includes/footer.php'; ?>