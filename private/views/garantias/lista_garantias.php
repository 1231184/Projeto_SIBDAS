<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

// --- LIGAÇÃO À BASE DE DADOS ---
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $resultados = $ligacao->query("
        SELECT
            gc.id_contrato,
            gc.tipo_cobertura,
            gc.referencia,
            gc.tipo_contrato,
            gc.periodicidade,
            gc.data_inicio,
            gc.data_fim,
            e.codigo_interno,
            e.designacao,
            f.nome_empresa AS entidade_responsavel
        FROM garantias_contratos gc
        INNER JOIN equipamentos e ON e.id_equipamento = gc.id_equipamento
        LEFT JOIN fornecedores f ON f.id_fornecedor = gc.id_entidade_responsavel
        ORDER BY gc.data_fim ASC
    ")->fetchAll(PDO::FETCH_OBJ);

    $erro = '';
} catch (PDOException $err) {
    $erro = "Erro na ligação à Base de Dados: " . $err->getMessage();
    $resultados = [];
}
$ligacao = null;
// --- FIM DA LIGAÇÃO ---
?>

<?php include '../../includes/header.php'; ?>

    <?php include '../../includes/sidebar.php'; ?>

    <main class="flex-grow-1 overflow-auto p-4 p-md-5">

        <header class="d-md-none d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-stethoscope fs-5 text-brand"></i>
                <h1 class="h5 fw-bold mb-0 text-dark">MedStock</h1>
            </div>
            <button class="btn btn-light border-0 shadow-sm d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"><i class="fa-solid fa-bars"></i></button>
        </header>

        <div class="mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">Garantias e Contratos</h1>
            <p class="text-muted small mb-0">Visão global das coberturas legais e de manutenção do parque tecnológico.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
            <div class="position-relative flex-grow-1" style="max-width: 350px;">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted"
                   style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" id="pesquisaGarantias" class="form-control ps-5 shadow-sm border-0"
                       placeholder="Pesquisar equipamento ou contrato..."
                       style="border-radius: 9px; padding-top: 10px; padding-bottom: 10px;">
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn-filter active" data-tipo="Todos">Todos</button>
                <button class="btn-filter" data-tipo="Garantia">Garantias</button>
                <button class="btn-filter" data-tipo="Contrato Manutenção">Contratos</button>
            </div>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="card dash-card border-0 shadow-sm p-5 text-center text-danger fw-bold">
                <i class="fa-solid fa-triangle-exclamation fs-1 mb-2 d-block"></i>
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php elseif (empty($resultados)): ?>
            <div class="card dash-card border-0 shadow-sm p-5 text-center">
                <div class="fs-1 mb-2">🛡️</div>
                <div class="fw-bold text-muted">Nenhuma garantia ou contrato registado.</div>
            </div>
        <?php else: ?>
            <div class="card dash-card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="tabelaDados">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipamento</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tipo</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Entidade Responsável</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Início</th>
                                <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Fim</th>
                                <th class="px-4 py-3 text-muted text-uppercase text-end" style="font-size: 0.7rem; letter-spacing: 0.5px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $gc): ?>
                                <?php
                                
                                $hoje     = new DateTime();
                                $dataFim  = new DateTime($gc->data_fim);
                                $diff     = $hoje->diff($dataFim);

                                if ($dataFim < $hoje) {
                                    $estadoHtml = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">Expirada</span>';
                                } elseif ($diff->days <= 30) {
                                    $estadoHtml = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i>A Expirar</span>';
                                } else {
                                    $estadoHtml = '<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">Ativa</span>';
                                }

                                // Tipo com ícone
                                if ($gc->tipo_cobertura === 'Garantia') {
                                    $tipoHtml = '<div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-shield-halved text-success"></i>
                                        <span class="text-dark fw-medium small">Garantia Legal</span>
                                    </div>';
                                } else {
                                    $ref       = !empty($gc->referencia)   ? '<span class="text-muted" style="font-size:0.65rem;">Ref: ' . htmlspecialchars($gc->referencia) . '</span>' : '';
                                    $tipoContr = !empty($gc->tipo_contrato) ? htmlspecialchars($gc->tipo_contrato) : 'Contrato';
                                    $tipoHtml  = '<div>
                                        <div class="text-dark fw-medium small lh-1">Contrato: ' . $tipoContr . '</div>
                                        ' . $ref . '
                                    </div>';
                                }
                                ?>
                                <tr data-tipo="<?= htmlspecialchars($gc->tipo_cobertura) ?>">
                                    <td class="px-4 py-3">
                                        <div class="text-brand fw-bold small"><?= htmlspecialchars($gc->codigo_interno) ?></div>
                                        <div class="text-dark fw-medium" style="font-size: 0.80rem;"><?= htmlspecialchars($gc->designacao) ?></div>
                                    </td>
                                    <td class="px-3 py-3"><?= $tipoHtml ?></td>
                                    <td class="px-3 py-3 text-muted small">
                                        <?= !empty($gc->entidade_responsavel) ? htmlspecialchars($gc->entidade_responsavel) : '—' ?>
                                    </td>
                                    <td class="px-3 py-3 text-dark small">
                                        <?= (new DateTime($gc->data_inicio))->format('d/m/Y') ?>
                                    </td>
                                    <td class="px-3 py-3 text-dark small">
                                        <?= $dataFim->format('d/m/Y') ?>
                                    </td>
                                    <td class="px-4 py-3 text-end"><?= $estadoHtml ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </main>

<?php include '../../includes/footer.php'; ?>