<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $hoje = date('Y-m-d');

    // --- CARD 1: INVENTÁRIO GLOBAL ---
    $stmtInv = $ligacao->query("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN estado = 'Ativo' THEN 1 ELSE 0 END) AS ativos,
            SUM(CASE WHEN estado = 'Em Manutenção' THEN 1 ELSE 0 END) AS manutencao,
            SUM(CASE WHEN estado = 'Em Calibração' THEN 1 ELSE 0 END) AS calibracao,
            SUM(CASE WHEN estado = 'Inativo' THEN 1 ELSE 0 END) AS inativos
        FROM equipamentos
        WHERE estado != 'Abatido'
    ");
    $inv = $stmtInv->fetch(PDO::FETCH_OBJ);

    // --- CARD 2: SUPORTE DE VIDA ---
    $stmtSV = $ligacao->query("
        SELECT COUNT(*) AS total
        FROM equipamentos
        WHERE criticidade = 'Suporte de Vida'
          AND estado NOT IN ('Abatido', 'Inativo')
    ");
    $sv = $stmtSV->fetch(PDO::FETCH_OBJ);

    // --- CARD 3: GARANTIAS E CONTRATOS ---
    $stmtGar = $ligacao->query("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN data_fim < '$hoje' THEN 1 ELSE 0 END) AS expiradas,
            SUM(CASE WHEN data_fim >= '$hoje' AND data_fim <= DATE_ADD('$hoje', INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS a_expirar
        FROM garantias_contratos
    ");
    $gar = $stmtGar->fetch(PDO::FETCH_OBJ);

    // --- CARD 4: DOCUMENTAÇÃO EM FALTA ---
    $stmtDoc = $ligacao->query("
        SELECT COUNT(*) AS total
        FROM equipamentos
        WHERE estado != 'Abatido'
          AND (falta_declaracao_ce = 1 OR falta_manual_utilizador = 1 OR falta_fatura_guia = 1)
    ");
    $docFalta = $stmtDoc->fetch(PDO::FETCH_OBJ);

    // --- GRÁFICO 1: EQUIPAMENTOS POR CATEGORIA ---
    $stmtCat = $ligacao->query("
        SELECT categoria, COUNT(*) AS total
        FROM equipamentos
        WHERE estado != 'Abatido'
        GROUP BY categoria
        ORDER BY total DESC
    ");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_OBJ);

    // --- GRÁFICO 2: EQUIPAMENTOS POR SERVIÇO ---
    $stmtServ = $ligacao->query("
        SELECT
            s.nome AS servico,
            SUM(CASE WHEN e.criticidade = 'Suporte de Vida' THEN 1 ELSE 0 END) AS suporte_vida,
            SUM(CASE WHEN e.criticidade != 'Suporte de Vida' THEN 1 ELSE 0 END) AS outros
        FROM equipamentos e
        INNER JOIN servicos s ON s.id_servico = e.id_servico
        WHERE e.estado != 'Abatido'
        GROUP BY s.id_servico, s.nome
        ORDER BY (suporte_vida + outros) DESC
    ");
    $servicos = $stmtServ->fetchAll(PDO::FETCH_OBJ);

    // --- ALERTAS ---
    $stmtAlertas = $ligacao->query("
        SELECT e.designacao, e.codigo_interno, 'Em Manutenção' AS tipo_alerta
        FROM equipamentos e
        WHERE e.estado = 'Em Manutenção'

        UNION

        SELECT e.designacao, e.codigo_interno, 'Documentação em Falta' AS tipo_alerta
        FROM equipamentos e
        WHERE e.estado != 'Abatido'
          AND (e.falta_declaracao_ce = 1 OR e.falta_manual_utilizador = 1 OR e.falta_fatura_guia = 1)

        UNION

        SELECT e.designacao, e.codigo_interno, 'Garantia Expirada' AS tipo_alerta
        FROM equipamentos e
        INNER JOIN garantias_contratos gc ON gc.id_equipamento = e.id_equipamento
        WHERE gc.data_fim < '$hoje'
          AND e.estado != 'Abatido'

        UNION

        SELECT e.designacao, e.codigo_interno, 'Garantia a Expirar' AS tipo_alerta
        FROM equipamentos e
        INNER JOIN garantias_contratos gc ON gc.id_equipamento = e.id_equipamento
        WHERE gc.data_fim >= '$hoje'
          AND gc.data_fim <= DATE_ADD('$hoje', INTERVAL 30 DAY)
          AND e.estado != 'Abatido'

        ORDER BY tipo_alerta ASC
        LIMIT 10
    ");
    $alertas = $stmtAlertas->fetchAll(PDO::FETCH_OBJ);

    $erroDb = '';
} catch (PDOException $err) {
    $erroDb = 'Erro na ligação à base de dados.';
    $inv = $sv = $gar = $docFalta = null;
    $categorias = $servicos = $alertas = [];
}
$ligacao = null;
?>

<?php include '../../includes/header.php'; ?>

    <?php include '../../includes/sidebar.php'; ?>

    <!-- MAIN -->
    <main class="flex-grow-1 overflow-auto p-4 p-md-5 bg-backend">

        <header class="d-md-none d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="/sibdas/1231184/medstock-solutions/assets/img/logotipo.png" alt="MedStock Logo" style="height: 45px; width: auto;">
            </div>
            <button class="btn btn-light border-0 shadow-sm d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"><i class="fa-solid fa-bars"></i></button>
        </header>

        <div class="mb-4">
            <h1 class="h2 fw-bold text-dark mb-1">Dashboard</h1>
            <p class="text-muted mb-0 fs-6">Visão geral e indicadores de síntese do parque tecnológico hospitalar.</p>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Inventário Global</p>
                            <h2 class="fw-bold text-dark mb-0 fs-1"><?= $inv ? number_format($inv->total) : '—' ?></h2>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-center mt-3 pt-3 border-top">
                        <div>
                            <a href="../equipamentos/lista_equi.php?filtro=estado:Ativo" class="text-decoration-none">
                                <div class="text-success fw-bold small"><?= $inv ? $inv->ativos : '—' ?></div>
                                <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Ativos</div>
                            </a>
                        </div>
                        <div>
                            <a href="../equipamentos/lista_equi.php?filtro=estado:Em Manutenção" class="text-decoration-none">
                                <div class="text-warning text-darken fw-bold small"><?= $inv ? $inv->manutencao : '—' ?></div>
                                <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Manutenção</div>
                            </a>
                        </div>
                        <div>
                            <a href="../equipamentos/lista_equi.php?filtro=estado:Em Calibração" class="text-decoration-none">
                                <div class="text-info fw-bold small"><?= $inv ? $inv->calibracao : '—' ?></div>
                                <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Calibração</div>
                            </a>
                        </div>
                        <div>
                            <a href="../equipamentos/lista_equi.php?filtro=estado:Inativo" class="text-decoration-none">
                                <div class="text-secondary fw-bold small"><?= $inv ? $inv->inativos : '—' ?></div>
                                <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Inativos</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <a href="../equipamentos/lista_equi.php?filtro=criticidade:Suporte de Vida" class="text-decoration-none">
                <div class="card dash-card h-100 border-0 shadow-sm p-4 border-bottom border-3 border-danger" style="cursor:pointer;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Suporte de Vida</p>
                            <h2 class="fw-bold text-danger mb-0 fs-1"><?= $sv ? $sv->total : '—' ?></h2>
                        </div>
                        <div class="rounded-4 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-heart-pulse fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-dark small fw-medium">Equipamentos Críticos Ativos</span>
                    </div>
                </div>
                </a>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Garantias e Contratos</p>
                            <h2 class="fw-bold text-dark mb-0 fs-1"><?= $gar ? $gar->total : '—' ?></h2>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-center mt-3 pt-3 border-top">
                        <div>
                            <div class="text-danger fw-bold small"><?= $gar ? $gar->expiradas : '—' ?></div>
                            <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Expiradas</div>
                        </div>
                        <div>
                            <div class="text-warning text-darken fw-bold small"><?= $gar ? $gar->a_expirar : '—' ?></div>
                            <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">A Expirar (30d)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Documentação em Falta</p>
                            <h2 class="fw-bold text-info text-darken mb-0 fs-1"><?= $docFalta ? $docFalta->total : '—' ?></h2>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-muted small fw-medium">Registos incompletos</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-xl-5 col-lg-6">
                <div class="card dash-card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold text-dark mb-1">Equipamentos por Categoria</h5>
                    <p class="text-muted small mb-4">Distribuição proporcional por tipo funcional</p>
                    <div class="position-relative w-100 m-auto" style="height: 250px;">
                        <canvas id="categoriaChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6">
                <div class="card dash-card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Alertas de Gestão</h5>
                            <p class="text-muted small mb-0">Ações e inconformidades que requerem atenção</p>
                        </div>
                        <span class="badge bg-danger rounded-pill"><?= count($alertas) ?> Ações</span>
                    </div>
                    
                    <div class="d-flex flex-column gap-2 overflow-auto pe-2" style="max-height: 250px;">
                        <?php if (empty($alertas)): ?>
                            <p class="text-muted small mb-0">Sem alertas activos.</p>
                        <?php else: ?>
                            <?php foreach ($alertas as $alerta): ?>
                                <?php
                                $badgeClass = match($alerta->tipo_alerta) {
                                    'Em Manutenção'       => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle text-darken',
                                    'Documentação em Falta' => 'bg-info bg-opacity-10 text-info border border-info-subtle',
                                    'Garantia Expirada'   => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
                                    'Garantia a Expirar'  => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle text-darken',
                                    default               => 'bg-secondary bg-opacity-10 text-secondary border'
                                };
                                ?>
                                <div class="p-3 bg-light rounded-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark small"><?= htmlspecialchars($alerta->designacao) ?></h6>
                                            <p class="text-muted mb-0 font-monospace" style="font-size: 0.75rem;"><?= htmlspecialchars($alerta->codigo_interno) ?></p>
                                        </div>
                                        <span class="badge <?= $badgeClass ?> px-2 py-1"><?= htmlspecialchars($alerta->tipo_alerta) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 pb-4">
            <div class="col-12">
                <div class="card dash-card border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-dark mb-1">Equipamentos por Localização</h5>
                    <p class="text-muted small mb-4">Distribuição por serviço hospitalar (A base a vermelho indica a proporção de Suporte de Vida)</p>
                    
                    <div style="height: 350px; width: 100%; position: relative;">
                        <canvas id="servicosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        const dadosCategorias = <?= json_encode(array_map(fn($c) => ['label' => $c->categoria, 'valor' => (int)$c->total], $categorias)) ?>;
        const dadosServicos   = <?= json_encode(array_map(fn($s) => ['label' => $s->servico, 'sv' => (int)$s->suporte_vida, 'outros' => (int)$s->outros], $servicos)) ?>;

        document.addEventListener("DOMContentLoaded", function() {

            const canvasCategoria = document.getElementById('categoriaChart');
            const canvasServicos  = document.getElementById('servicosChart');

            if (canvasCategoria) {
                const cores = ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#0dcaf0','#fd7e14'];
                new Chart(canvasCategoria.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels:   dadosCategorias.map(d => d.label),
                        datasets: [{
                            data:            dadosCategorias.map(d => d.valor),
                            backgroundColor: dadosCategorias.map((_, i) => cores[i % cores.length]),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, padding: 15, font: { size: 11, family: "'Inter', sans-serif" } }
                            }
                        },
                        cutout: '60%',
                        onClick: (evt, elements) => {
                            if (elements.length > 0) {
                                const categoria = dadosCategorias[elements[0].index].label;
                                window.location.href = '../equipamentos/lista_equi.php?filtro=categoria:' + encodeURIComponent(categoria);
                            }
                        },
                        onHover: (evt, elements) => {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            }

            if (canvasServicos) {
                new Chart(canvasServicos.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels:   dadosServicos.map(d => d.label),
                        datasets: [
                            {
                                label: 'Suporte de Vida',
                                data:  dadosServicos.map(d => d.sv),
                                backgroundColor: '#dc3545',
                                stack: 'ServicosStack',
                                maxBarThickness: 45
                            },
                            {
                                label: 'Outros Equipamentos',
                                data:  dadosServicos.map(d => d.outros),
                                backgroundColor: '#4169a1',
                                stack: 'ServicosStack',
                                borderRadius: { topLeft: 6, topRight: 6 },
                                maxBarThickness: 45
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11, family: "'Inter', sans-serif" } } },
                            y: { stacked: true, beginAtZero: true, grid: { borderDash: [4,4], color: '#e9ecef' }, ticks: { stepSize: 1, precision: 0, font: { size: 11, family: "'Inter', sans-serif" } } }
                        },
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 12, padding: 15, font: { size: 11, family: "'Inter', sans-serif" } } }
                        },
                        onClick: (evt, elements) => {
                            if (elements.length > 0) {
                                const idx = elements[0].index;
                                const datasetIdx = elements[0].datasetIndex;
                                const servico = dadosServicos[idx].label;
                                if (datasetIdx === 0) {
                                   
                                    window.location.href = '../equipamentos/lista_equi.php?filtro=criticidade:Suporte de Vida&filtro2=servico:' + encodeURIComponent(servico);
                                } else {
                                    
                                    window.location.href = '../equipamentos/lista_equi.php?filtro=servico:' + encodeURIComponent(servico);
                                }
                            }
                        },
                        onHover: (evt, elements) => {
                            evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            }
        });
    </script>


<?php include '../../includes/footer.php'; ?>