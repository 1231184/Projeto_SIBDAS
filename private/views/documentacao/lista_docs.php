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
            d.id_documento,
            d.tipo_documento,
            d.titulo,
            d.caminho_ficheiro,
            d.data_emissao,
            d.data_validade,
            e.codigo_interno,
            e.designacao
        FROM documentos d
        INNER JOIN equipamentos e ON e.id_equipamento = d.id_equipamento
        ORDER BY d.data_upload DESC
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
        <h1 class="h3 fw-bold text-dark mb-1">Documentação</h1>
        <p class="text-muted small mb-0">Consulta global de todos os documentos técnicos associados aos equipamentos.</p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        <div class="position-relative flex-grow-1" style="max-width: 350px;">
            <i class="fa-solid fa-magnifying-glass position-absolute text-muted"
                style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
            <input type="text" id="pesquisaDocs" class="form-control ps-5 shadow-sm border-0"
                placeholder="Pesquisar documento ou equipamento..."
                style="border-radius: 9px; padding-top: 10px; padding-bottom: 10px;">
        </div>
        <div class="d-flex gap-2 flex-wrap">
    <button class="btn-filter active" data-tipo="Todos">Todos</button>
    <button class="btn-filter" data-tipo="Manual de Utilizador">Manuais de Utilizador</button>
    <button class="btn-filter" data-tipo="Manual de Serviço">Manuais de Serviço</button>
    <button class="btn-filter" data-tipo="Certificado de Calibração">Certificados de Calibração</button>
    <button class="btn-filter" data-tipo="Declaração CE">Declarações CE</button>
    <button class="btn-filter" data-tipo="Contrato de Manutenção">Contratos</button>
    <button class="btn-filter" data-tipo="Fatura">Faturas</button>
</div>
        <span id="txtResultados" class="text-muted small ms-auto"></span>
    </div>

    <?php if (!empty($erro)): ?>
        <div class="card dash-card border-0 shadow-sm p-5 text-center text-danger fw-bold">
            <i class="fa-solid fa-triangle-exclamation fs-1 mb-2 d-block"></i>
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php elseif (empty($resultados)): ?>
        <div class="card dash-card border-0 shadow-sm p-5 text-center">
            <div class="fs-1 mb-2">📄</div>
            <div class="fw-bold text-muted">Nenhum documento registado.</div>
        </div>
    <?php else: ?>
        <div class="card dash-card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="tabelaDados">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Documento</th>
                            <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipamento Associado</th>
                            <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Data de Emissão</th>
                            <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Validade</th>
                            <th class="px-4 py-3 text-muted text-uppercase text-end" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $doc): ?>
                            <?php
                            $badgeClass = 'bg-secondary';
                            switch ($doc->tipo_documento) {
                                case 'Manual de utilizador':
                                    $badgeClass = 'bg-secondary';
                                    break;
                                case 'Certificado de calibração':
                                    $badgeClass = 'bg-info';
                                    break;
                                case 'Declaração de conformidade':
                                    $badgeClass = 'bg-primary';
                                    break;
                                case 'Contrato de manutenção':
                                    $badgeClass = 'bg-success';
                                    break;
                                case 'Fatura ou guia de aquisição':
                                    $badgeClass = 'bg-warning text-dark';
                                    break;
                            }

                            $validadeHtml = '<span class="text-muted small">N/A</span>';
                            if (!empty($doc->data_validade)) {
                                $hoje     = new DateTime();
                                $validade = new DateTime($doc->data_validade);
                                $diff     = $hoje->diff($validade);
                                $dataFmt  = $validade->format('d/m/Y');

                                if ($validade < $hoje) {
                                    $validadeHtml = '<span class="text-danger fw-bold small"><i class="fa-solid fa-circle-exclamation me-1"></i>' . $dataFmt . ' (Expirado)</span>';
                                } elseif ($diff->days <= 30) {
                                    $validadeHtml = '<span class="text-warning fw-bold small"><i class="fa-solid fa-triangle-exclamation me-1"></i>' . $dataFmt . ' (30 dias)</span>';
                                } else {
                                    $validadeHtml = '<span class="text-success fw-bold small">' . $dataFmt . '</span>';
                                }
                            }

                            $emissaoHtml = '<span class="text-muted small">—</span>';
                            if (!empty($doc->data_emissao)) {
                                $emissaoHtml = '<span class="text-dark small">' . (new DateTime($doc->data_emissao))->format('d/m/Y') . '</span>';
                            }

                            $titulo = !empty($doc->titulo) ? htmlspecialchars($doc->titulo) : htmlspecialchars($doc->caminho_ficheiro);
                            ?>
                            <tr data-tipo="<?= htmlspecialchars($doc->tipo_documento) ?>">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="fa-solid fa-file-pdf text-danger fs-4 mt-1 flex-shrink-0"></i>
                                        <div>
                                            <span class="badge <?= $badgeClass ?> mb-1" style="font-size: 0.65rem;">
                                                <?= htmlspecialchars($doc->tipo_documento) ?>
                                            </span>
                                            <div class="fw-bold text-dark small"><?= $titulo ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($doc->caminho_ficheiro) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-brand fw-bold small"><?= htmlspecialchars($doc->codigo_interno) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($doc->designacao) ?></div>
                                </td>
                                <td class="px-3 py-3"><?= $emissaoHtml ?></td>
                                <td class="px-3 py-3"><?= $validadeHtml ?></td>
                                <td class="px-4 py-3 text-end">
                                    <a href="<?= BASE_URL . '/' . htmlspecialchars($doc->caminho_ficheiro) ?>"
                                        download
                                        class="btn btn-sm btn-light border shadow-sm text-brand fw-medium">
                                        <i class="fa-solid fa-download me-1"></i> Descarregar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php include '../../includes/footer.php'; ?>