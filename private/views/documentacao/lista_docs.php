<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();
?>

<?php include '../../includes/header.php'; ?>

    <?php include '../../includes/sidebar.php'; ?>

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
                <h1 class="h3 fw-bold text-dark mb-1">Documentação</h1>
                <p class="text-muted small mb-0">Consulta global de todos os documentos técnicos associados aos equipamentos.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <div class="position-relative flex-grow-1" style="max-width: 350px;">
                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" class="form-control ps-5 shadow-sm border-0" placeholder="Pesquisar documento ou equipamento..." style="border-radius: 9px; padding-top: 10px; padding-bottom: 10px;">
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-filter active">Todos</button>
                    <button class="btn-filter">Manuais</button>
                    <button class="btn-filter">Certificados de Calibração</button>
                    <button class="btn-filter">Declarações CE</button>
                </div>
            </div>

            <div class="card dash-card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Documento</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipamento Associado</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Validade</th>
                                <th class="px-4 py-3 text-muted text-uppercase text-end" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="fa-solid fa-file-pdf text-danger fs-4 mt-1"></i>
                                        <div>
                                            <span class="badge bg-secondary mb-1" style="font-size: 0.65rem;">Manual de Utilizador</span>
                                            <div class="fw-bold text-dark small mb-0">Manual_Operacao_v2.pdf</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-brand fw-bold small">EQ-2024-001</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Monitor Multiparamétrico</div>
                                </td>
                                <td class="px-3 py-3 text-muted small">N/A</td>
                                <td class="px-4 py-3 text-end">
                                    <button class="btn btn-sm btn-light border shadow-sm text-brand fw-medium"><i class="fa-solid fa-download me-1"></i> Descarregar</button>
                                </td>
                            </tr>

                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="fa-solid fa-file-pdf text-danger fs-4 mt-1"></i>
                                        <div>
                                            <span class="badge bg-info mb-1" style="font-size: 0.65rem;">Certificado de Calibração</span>
                                            <div class="fw-bold text-dark small mb-0">Cert_Calib_2024.pdf</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-brand fw-bold small">EQ-0042</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Ventilador Pulmonar</div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="text-dark fw-bold small">15/01/2025</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <button class="btn btn-sm btn-light border shadow-sm text-brand fw-medium"><i class="fa-solid fa-download me-1"></i> Descarregar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kpiCards = document.querySelectorAll('.kpi-doc-card');
            const typeBtns = document.querySelectorAll('.btn-doc-filter');
            const searchInput = document.getElementById('pesquisaDocs');
            const tableRows = document.querySelectorAll('#tabelaDocs tr:not(#noResultsRowDocs)');
            const noResultsRow = document.getElementById('noResultsRowDocs');
            const textResultados = document.getElementById('txtResultados');
            const btnLimpar = document.getElementById('btnLimparDocs');

            // Estado atual dos filtros
            let filtroValidade = 'Todos';
            let filtroTipo = 'Todos';

            function aplicarFiltros() {
                const termo = searchInput.value.toLowerCase().trim();
                let visiveis = 0;

                tableRows.forEach(row => {
                    const rowText = row.innerText.toLowerCase();
                    const dValidade = row.dataset.validade;
                    const dTipo = row.dataset.tipo;

                    const matchSearch = termo === '' || rowText.includes(termo);
                    const matchValidade = filtroValidade === 'Todos' || dValidade === filtroValidade;
                    const matchTipo = filtroTipo === 'Todos' || dTipo === filtroTipo;

                    if(matchSearch && matchValidade && matchTipo) {
                        row.classList.remove('d-none');
                        visiveis++;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                // Atualizar UI
                textResultados.innerText = `Mostrando ${visiveis} documento${visiveis !== 1 ? 's' : ''}`;
                
                if(visiveis === 0) noResultsRow.classList.remove('d-none');
                else noResultsRow.classList.add('d-none');

                if(filtroValidade !== 'Todos' || filtroTipo !== 'Todos' || termo !== '') {
                    btnLimpar.classList.remove('d-none');
                } else {
                    btnLimpar.classList.add('d-none');
                }
            }

            // Clique nos cartões KPI (Validade)
            kpiCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Se clicar no que já está ativo, desmarca e volta a 'Todos'
                    if(this.classList.contains(this.dataset.activeClass) && this.dataset.valFilter !== 'Todos') {
                        this.classList.remove(this.dataset.activeClass);
                        filtroValidade = 'Todos';
                    } else {
                        // Limpa ativos
                        kpiCards.forEach(c => {
                            if(c.dataset.activeClass) c.classList.remove(c.dataset.activeClass);
                        });
                        // Ativa o clicado se não for o 'Todos'
                        if(this.dataset.valFilter !== 'Todos') {
                            this.classList.add(this.dataset.activeClass);
                        }
                        filtroValidade = this.dataset.valFilter;
                    }
                    aplicarFiltros();
                });
            });

            // Clique nas Pills (Tipo)
            typeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    typeBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filtroTipo = this.dataset.tipo;
                    aplicarFiltros();
                });
            });

            // Digitar na pesquisa
            if(searchInput) searchInput.addEventListener('input', aplicarFiltros);

            // Botão Limpar Filtros
            btnLimpar.addEventListener('click', () => {
                filtroValidade = 'Todos';
                filtroTipo = 'Todos';
                searchInput.value = '';
                
                kpiCards.forEach(c => { if(c.dataset.activeClass) c.classList.remove(c.dataset.activeClass); });
                typeBtns.forEach(b => b.classList.remove('active'));
                document.querySelector('.btn-doc-filter[data-tipo="Todos"]').classList.add('active');
                
                aplicarFiltros();
            });
        });
    </script>


<?php include '../../includes/footer.php'; ?>