<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$filtro_servico = isset($_GET['id_servico']) ? (int)$_GET['id_servico'] : null;
$filtro_sala    = isset($_GET['id_sala'])    ? (int)$_GET['id_sala']    : null;

// Ficha 13: contexto de origem para o botão "Voltar ao Fornecedor"
$origem        = $_GET['origem'] ?? 'equipamentos';
$id_fornecedor = isset($_GET['id_fornecedor']) ? (int)$_GET['id_fornecedor'] : null;

if ($origem === 'fornecedor' && $id_fornecedor) {
    $urlVoltar   = '../fornecedores/lista_fornecedores.php?abrir=' . $id_fornecedor;
    $textoVoltar = 'Voltar ao Fornecedor';
} else {
    $urlVoltar   = null;
    $textoVoltar = null;
}

$mensagem_sucesso = "";
if (isset($_GET['sucesso']) && $_GET['sucesso'] == "1") {
    $mensagem_sucesso = "Equipamento registado com sucesso!";
}

// --- INÍCIO DA LIGAÇÃO À BASE DE DADOS ---
try {
    // Cria a ligação usando as credenciais do config.php (AGORA COM A PORTA INCLUÍDA!)
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    // Diz ao PDO para atirar erros se algo falhar
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Faz a consulta (Query) para ir buscar todos os equipamentos!
    $sql_equip = "SELECT * FROM equipamentos WHERE 1=1";
if ($filtro_sala)    $sql_equip .= " AND id_sala = " . $filtro_sala;
elseif ($filtro_servico) $sql_equip .= " AND id_servico = " . $filtro_servico;
$sql_equip .= " ORDER BY codigo_interno ASC";
$resultados = $ligacao->query($sql_equip)->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    // Se a password estiver errada ou a BD em baixo, ele captura o erro aqui!
    $erro = "Aconteceu um erro na ligação à Base de Dados: " . $err->getMessage();
    $resultados = [];
}

// Fecha a Ligação
$ligacao = null;
// --- FIM DA LIGAÇÃO À BASE DE DADOS ---
?>



<?php include '../../includes/header.php'; ?>


<?php include '../../includes/sidebar.php'; ?>

<main class="d-flex flex-column flex-grow-1 overflow-hidden bg-backend">

    <header class="d-md-none d-flex align-items-center justify-content-between p-4 pb-3 border-bottom bg-white">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-stethoscope fs-5 text-brand"></i>
            <h1 class="h5 fw-bold mb-0 text-dark">MedStock</h1>
        </div>
        <button class="btn btn-light border-0 shadow-sm"><i class="fa-solid fa-bars"></i></button>
    </header>

    <div
        class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between flex-shrink-0 z-1">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Equipamentos</h1>
            <?php if (!empty($mensagem_sucesso)): ?>
                <div id="alertaSucesso" class="alert alert-success alert-dismissible fade show shadow position-fixed top-0 start-50 translate-middle-x mt-4 d-flex align-items-center" role="alert" style="z-index: 1080; min-width: 360px; max-width: 600px;">
                    <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                    <span class="fw-medium"><?= htmlspecialchars($mensagem_sucesso) ?></span>
                    <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>

                <script>
                    // Limpar o ?sucesso=1 do URL para que F5 não volte a mostrar a mensagem
                    if (window.history.replaceState) {
                        const urlLimpa = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({
                            path: urlLimpa
                        }, '', urlLimpa);
                    }

                    // Auto-fechar a mensagem ao fim de 4 segundos com fade suave
                    setTimeout(function() {
                        const alerta = document.getElementById('alertaSucesso');
                        if (alerta) {
                            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
                            bsAlert.close();
                        }
                    }, 4000);
                </script>
            <?php endif; ?>
            <div class="text-muted small" id="totalRegistos">
                <?= count($resultados) ?> <?= count($resultados) == 1 ? 'equipamento registado' : 'equipamentos registados' ?>
            </div>
        </div>
        <a href="novo.php" class="btn btn-brand d-inline-flex align-items-center gap-2 shadow-sm fw-bold">
            <i class="fa-solid fa-plus"></i> Novo Equipamento
        </a>
    </div>

    <div class="equipamentos-wrapper">

        <aside class="filter-panel d-flex flex-column" id="filterSidebar">
            <div
                class="p-3 border-bottom d-flex align-items-center justify-content-between bg-white sticky-top flex-shrink-0">
                <div class="fw-bold text-dark small d-flex align-items-center gap-2">
                    Filtros
                    <span id="badgeContadorFiltros" class="badge bg-brand rounded-pill d-none">0</span>
                </div>
                <button id="btnLimparFiltros"
                    class="btn btn-link text-danger text-decoration-none p-0 small fw-bold d-none"
                    style="font-size: 0.7rem;">Limpar</button>
            </div>

            <div class="accordion filter-accordion overflow-auto" id="accordionFiltros">

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold text-uppercase shadow-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseEstado">
                            Estado
                        </button>
                    </h2>
                    <div id="collapseEstado" class="accordion-collapse collapse show"
                        data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <label class="filter-check"><input type="checkbox" value="Ativo" data-group="estado">
                                <div class="custom-box"></div><span class="label-text">Ativo</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Em Manutenção"
                                    data-group="estado">
                                <div class="custom-box"></div><span class="label-text">Em Manutenção</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Em Calibração"
                                    data-group="estado">
                                <div class="custom-box"></div><span class="label-text">Em Calibração</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Inativo" data-group="estado">
                                <div class="custom-box"></div><span class="label-text">Inativo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-uppercase shadow-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseCriticidade">
                            Criticidade
                        </button>
                    </h2>
                    <div id="collapseCriticidade" class="accordion-collapse collapse"
                        data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <label class="filter-check"><input type="checkbox" value="Suporte de Vida"
                                    data-group="criticidade">
                                <div class="custom-box"></div><span class="label-text">Suporte de Vida</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Alta"
                                    data-group="criticidade">
                                <div class="custom-box"></div><span class="label-text">Alta</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Média"
                                    data-group="criticidade">
                                <div class="custom-box"></div><span class="label-text">Média</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Baixa"
                                    data-group="criticidade">
                                <div class="custom-box"></div><span class="label-text">Baixa</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-uppercase shadow-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseCategoria">
                            Categoria
                        </button>
                    </h2>
                    <div id="collapseCategoria" class="accordion-collapse collapse"
                        data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <label class="filter-check"><input type="checkbox" value="Monitorização"
                                    data-group="categoria">
                                <div class="custom-box"></div><span class="label-text">Monitorização</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Terapia"
                                    data-group="categoria">
                                <div class="custom-box"></div><span class="label-text">Terapia</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Diagnóstico"
                                    data-group="categoria">
                                <div class="custom-box"></div><span class="label-text">Diagnóstico</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-uppercase shadow-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFornecedor">
                            Fornecedor
                        </button>
                    </h2>
                    <div id="collapseFornecedor" class="accordion-collapse collapse"
                        data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <label class="filter-check"><input type="checkbox" value="Dräger Portugal"
                                    data-group="fornecedor">
                                <div class="custom-box"></div><span class="label-text">Dräger Portugal</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Philips Healthcare PT"
                                    data-group="fornecedor">
                                <div class="custom-box"></div><span class="label-text">Philips Healthcare PT</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="MedServ Técnica"
                                    data-group="fornecedor">
                                <div class="custom-box"></div><span class="label-text">MedServ Técnica</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="TechMed Solutions"
                                    data-group="fornecedor">
                                <div class="custom-box"></div><span class="label-text">TechMed Solutions</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="IberiaMed Serviços"
                                    data-group="fornecedor">
                                <div class="custom-box"></div><span class="label-text">IberiaMed Serviços</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-uppercase shadow-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseServico">
                            Serviço
                        </button>
                    </h2>
                    <div id="collapseServico" class="accordion-collapse collapse"
                        data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <label class="filter-check"><input type="checkbox" value="Urgência Geral"
                                    data-group="servico">
                                <div class="custom-box"></div><span class="label-text">Urgência Geral</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Imagiologia"
                                    data-group="servico">
                                <div class="custom-box"></div><span class="label-text">Imagiologia</span>
                            </label>
                            <label class="filter-check"><input type="checkbox"
                                    value="Unidade de Cuidados Intensivos (UCI)" data-group="servico">
                                <div class="custom-box"></div><span class="label-text">UCI</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Bloco Operatório"
                                    data-group="servico">
                                <div class="custom-box"></div><span class="label-text">Bloco Operatório</span>
                            </label>
                            <label class="filter-check"><input type="checkbox" value="Medicina Interna"
                                    data-group="servico">
                                <div class="custom-box"></div><span class="label-text">Medicina Interna</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </aside>

        <section class="flex-grow-1 p-4 overflow-auto d-flex flex-column">

            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                <button id="btnToggleSidebar"
                    class="btn btn-light bg-white border shadow-sm text-secondary fw-bold small d-flex align-items-center gap-2 flex-shrink-0"
                    style="font-size: 0.8rem;">
                    <i class="fa-solid fa-sliders"></i> <span id="textToggleSidebar">Ocultar filtros</span>
                </button>

                <div class="position-relative flex-grow-1" style="min-width: 250px;">
                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted"
                        style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" id="inputPesquisa" class="form-control ps-5 shadow-sm border-0"
                        placeholder="Pesquisar por código, equipamento, marca ou serviço..."
                        style="border-radius: 9px; padding-top: 9px; padding-bottom: 9px; font-size: 0.85rem;">
                </div>
            </div>

            <div id="activePillsContainer" class="d-flex flex-wrap gap-2 mb-3"></div>

            <div class="card border-0 shadow-sm overflow-hidden flex-shrink-0">
                <div class="table-responsive">
                    <table id="tabelaDados" class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-3 text-muted text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.7px;">Código</th>
                                <th class="px-3 py-3 text-muted text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.7px;">Equipamento</th>
                                <th class="px-3 py-3 text-muted text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.85px;">Localização</th>
                                <th class="px-3 py-3 text-muted text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.7px;">Criticidade</th>
                                <th class="px-3 py-3 text-muted text-uppercase"
                                    style="font-size: 0.8rem; letter-spacing: 0.7px;">Estado</th>
                                <th class="px-3 py-3 text-muted text-uppercase text-end"
                                    style="font-size: 0.8rem; letter-spacing: 0.7px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaEquipamentos">
                            <?php if (!empty($erro)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-danger fw-bold">
                                        <i class="fa-solid fa-triangle-exclamation fs-1 mb-2"></i><br>
                                        <?= htmlspecialchars($erro) ?>
                                    </td>
                                </tr>
                            <?php elseif (empty($resultados)): ?>
                                <tr id="noResultsRow">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="fs-1 mb-2">🔍</div>
                                        <div class="fw-bold text-muted">Nenhum equipamento encontrado na Base de Dados.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($resultados as $equip): ?>
                                    <tr data-estado="<?= htmlspecialchars($equip->estado) ?>" data-criticidade="<?= htmlspecialchars($equip->criticidade) ?>" data-categoria="<?= htmlspecialchars($equip->categoria) ?>">
                                        <td class="px-3 py-3 text-brand fw-bold" style="font-size: 1.0rem;">
                                            <?= htmlspecialchars($equip->codigo_interno) ?>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="fw-bold text-dark mb-0" style="font-size: 1.0rem;">
                                                <?= htmlspecialchars($equip->designacao) ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 1.0rem;">
                                                <?= htmlspecialchars($equip->marca . ' · ' . $equip->modelo) ?>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-dark fw-medium" style="font-size: 1.0rem;">
                                            Sala <?= htmlspecialchars($equip->id_sala ?? 'N/A') ?>
                                        </td>
                                        <?php
                                        // Lógica para as cores da Criticidade
                                        $classeCrit = 'bg-secondary text-white'; // default
                                        if ($equip->criticidade == 'Alta') $classeCrit = 'cr-alta';
                                        elseif ($equip->criticidade == 'Média') $classeCrit = 'cr-media';
                                        elseif ($equip->criticidade == 'Baixa') $classeCrit = 'cr-baixa';
                                        elseif ($equip->criticidade == 'Suporte de Vida') $classeCrit = 'cr-vida';

                                        // Lógica para as cores do Estado
                                        $classeEstado = 'bg-secondary text-white'; // default
                                        $pontoHmtl = '';
                                        if ($equip->estado == 'Ativo') {
                                            $classeEstado = 'st-ativo';
                                            $pontoHmtl = '<span class="dot"></span>';
                                        } elseif ($equip->estado == 'Em Manutenção') {
                                            $classeEstado = 'st-manutencao';
                                            $pontoHmtl = '<span class="dot"></span>';
                                        } elseif ($equip->estado == 'Em Calibração') {
                                            $classeEstado = 'st-calibracao';
                                            $pontoHmtl = '<span class="dot"></span>';
                                        }
                                        ?>

                                        <td class="px-3 py-3">
                                            <span class="badge-eq <?= $classeCrit ?>" style="font-size: 0.85rem; padding: 0.4rem 0.6rem;">
                                                <?= htmlspecialchars($equip->criticidade) ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="badge-eq <?= $classeEstado ?>" style="font-size: 0.85rem; padding: 0.4rem 0.6rem;">
                                                <?= $pontoHmtl ?><?= htmlspecialchars($equip->estado) ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button class="btn btn-sm btn-brand-subtle text-brand fw-bold shadow-none btn-ver-eq"
                                                    data-id="<?= $equip->id_equipamento ?>" style="font-size: 1.0rem;">
                                                    Ver
                                                </button>

                                                <button class="btn btn-sm btn-light border text-danger shadow-none btn-remover-eq"
                                                    data-id="<?= $equip->id_equipamento ?>" title="Remover">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>
</main>

<!-- Modal Detalhes Equipamento -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg bg-backend">

            <!-- Header do Modal -->
            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalDetalhesLabel">
                            Monitor Multiparamétrico de Sinais Vitais
                        </h5>
                        <span class="badge badge-soft-success">Ativo</span>
                        <span class="badge badge-soft-warning">Alta</span>
                    </div>
                    <p class="text-muted custom-monospace small mb-0">EQ-2024-001</p>
                </div>
                <div class="d-flex align-items-center gap-2">
    <?php if ($urlVoltar): ?>
        <a href="<?= htmlspecialchars($urlVoltar) ?>"
           class="btn-action-custom py-2 px-3 text-decoration-none">
            <i class="fa-solid fa-arrow-left me-2"></i><?= htmlspecialchars($textoVoltar) ?>
        </a>
    <?php endif; ?>
    <button class="btn-action-custom bg-white border text-dark"
        onclick="alert('🖨️ Comando enviado! A etiqueta com o código de barras do equipamento EQ-2024-001 foi gerada com sucesso.')">
        <i class="fa-solid fa-barcode me-1"></i> Etiqueta
    </button>
    <button class="btn-action-custom py-2 px-3" data-bs-toggle="modal" data-bs-target="#modalEditar"
        data-bs-dismiss="modal">
        <i class="fa-solid fa-pencil me-2"></i> Editar
    </button>
    <button class="btn-action-custom btn-action-danger" data-bs-toggle="modal"
        data-bs-target="#modalRemover">
        <i class="fa-solid fa-trash-can me-2"></i> Remover
    </button>
    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal"
        aria-label="Fechar"></button>
</div>
            </div>

            <!-- Body do Modal -->
            <div class="modal-body p-4">
                <div class="w-100" style="max-width: 1024px; margin: 0 auto;">

                    <!-- SEPARADORES (Nav Tabs) -->
                    <ul class="nav nav-tabs border-bottom-0" id="detalhesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark fw-medium" id="geral-tab" data-bs-toggle="tab"
                                data-bs-target="#geral-pane" type="button" role="tab" aria-selected="true">
                                Geral
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="localizacao-tab" data-bs-toggle="tab"
                                data-bs-target="#localizacao-pane" type="button" role="tab" aria-selected="false">
                                Localização
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="fornecedores-tab" data-bs-toggle="tab"
                                data-bs-target="#fornecedores-pane" type="button" role="tab" aria-selected="false">
                                Fornecedores
                                <span class="badge bg-secondary rounded-pill ms-1"
                                    style="font-size: 0.65rem;">3</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="garantias-tab" data-bs-toggle="tab"
                                data-bs-target="#garantias-pane" type="button" role="tab" aria-selected="false">
                                Garantias e Contratos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="documentos-tab" data-bs-toggle="tab"
                                data-bs-target="#documentos-pane" type="button" role="tab" aria-selected="false">
                                Documentos
                                <span class="badge bg-secondary rounded-pill ms-1"
                                    style="font-size: 0.65rem;">3</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="acessorios-tab" data-bs-toggle="tab"
                                data-bs-target="#acessorios-pane" type="button" role="tab" aria-selected="false">
                                Acessórios
                                <span class="badge bg-secondary rounded-pill ms-1"
                                    style="font-size: 0.65rem;">2</span>
                            </button>
                        </li>
                    </ul>

                    <!-- CONTEÚDO DOS SEPARADORES -->
                    <div class="card dash-card p-0 mb-3">
                        <div class="card-body p-4">
                            <div class="tab-content" id="detalhesTabsContent">

                                <!-- TAB 1: GERAL -->
                                <div class="tab-pane fade show active" id="geral-pane" role="tabpanel" tabindex="0">

                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Identificação e
                                        Fabrico</h5>
                                    <div class="row row-cols-2 row-cols-md-3 g-4 mb-4">
                                        <div class="col">
                                            <p class="detail-label">Categoria</p>
                                            <p class="detail-value">Monitorização</p>
                                        </div>
                                        <div class="col">
                                            <p class="detail-label">Criticidade Clínica</p>
                                            <p class="detail-value"><span
                                                    class="badge badge-soft-warning">Alta</span></p>
                                        </div>
                                        <div class="col">
                                            <p class="detail-label">Estado Atual</p>
                                            <p class="detail-value"><span class="badge badge-soft-success">Ativo /
                                                    Operacional</span></p>
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
                                            <p class="detail-value custom-monospace" style="font-size: 0.8rem;">
                                                SN-PH-2024-001</p>
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

                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2 mt-4">Receção e
                                        Aquisição</h5>
                                    <div class="row row-cols-2 row-cols-md-3 g-4 mb-4">
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
                                    </div>

                                    <h5 class="fw-semibold fs-6 mb-2 text-dark border-bottom pb-2 mt-4">Observações
                                    </h5>
                                    <p class="detail-value text-muted" style="font-size: 0.875rem;">Monitor de UCI
                                        com 12 derivações ECG. Equipamento encontra-se em excelente estado de
                                        conservação.</p>
                                </div>

                                <!-- TAB 2: LOCALIZAÇÃO -->
                                <div class="tab-pane fade" id="localizacao-pane" role="tabpanel" tabindex="0">
                                    <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Localização Atual</h5>
                                    <div class="row row-cols-2 row-cols-md-4 g-4 mb-4">
                                        <div class="col">
                                            <p class="detail-label">Edifício</p>
                                            <p class="detail-value d-flex align-items-center gap-1">
                                                <i class="fa-regular fa-building text-muted"></i> Edifício Principal
                                            </p>
                                        </div>
                                        <div class="col">
                                            <p class="detail-label">Piso</p>
                                            <p class="detail-value">Piso 1</p>
                                        </div>
                                        <div class="col">
                                            <p class="detail-label">Serviço</p>
                                            <p class="detail-value">Unidade de Cuidados Intensivos (UCI)</p>
                                        </div>
                                        <div class="col">
                                            <p class="detail-label">Sala / Compartimento</p>
                                            <p class="detail-value">Box 1</p>
                                        </div>
                                    </div>

                                    <div class="card dash-card shadow-sm border-0 bg-light">
                                        <div class="card-header border-0 bg-transparent pt-3 px-4 pb-2 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-clock-rotate-left text-muted" style="font-size: 1rem;"></i>
                                            <h6 class="card-title-custom mb-0">Histórico de Movimentações</h6>
                                        </div>
                                        <div class="card-body px-4 pb-4 pt-0">
                                            <div class="d-flex flex-column gap-2">

                                                <div class="list-box bg-white border d-flex justify-content-between align-items-center py-2">
                                                    <div>
                                                        <span class="fw-bold text-dark small">Transferência de Serviço</span>
                                                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Movido de: Serviço de Urgência &rarr; UCI (Unidade A)</p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">15/01/2024</p>
                                                        <p class="text-muted small mb-0" style="font-size: 0.65rem;">Por: admin</p>
                                                    </div>
                                                </div>

                                                <div class="list-box bg-white border d-flex justify-content-between align-items-center py-2">
                                                    <div>
                                                        <span class="fw-bold text-dark small">Entrada em Inventário</span>
                                                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Registo inicial e instalação física no Serviço de Urgência</p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">10/01/2024</p>
                                                        <p class="text-muted small mb-0" style="font-size: 0.65rem;">Por: admin</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: FORNECEDORES -->
                                <div class="tab-pane fade" id="fornecedores-pane" role="tabpanel" tabindex="0">
                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Entidades
                                        Associadas</h5>
                                    <div class="d-flex flex-column gap-2 mt-3">
                                        <a href="#" class="text-decoration-none text-dark">
                                            <div class="list-box d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="detail-value fw-medium">Philips Healthcare</p>
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        Fornecedor Comercial</p>
                                                </div>
                                                <div class="text-end d-none d-sm-block">
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        +351 210 000 002</p>
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        contacto@philips-saude.pt</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="text-decoration-none text-dark">
                                            <div class="list-box d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="detail-value fw-medium">MedServ Técnica</p>
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        Assistência Técnica Oficial</p>
                                                </div>
                                                <div class="text-end d-none d-sm-block">
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        +351 210 000 004</p>
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        suporte@medserv.pt</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="text-decoration-none text-dark">
                                            <div class="list-box d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="detail-value fw-medium">FarmaMed Consumíveis</p>
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        Fornecedor de Consumíveis</p>
                                                </div>
                                                <div class="text-end d-none d-sm-block">
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        +351 210 000 008</p>
                                                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                        geral@farmamed.pt</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- TAB 4: GARANTIAS E CONTRATOS -->
                                <div class="tab-pane fade" id="garantias-pane" role="tabpanel" tabindex="0">

                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">
                                        <i class="fa-solid fa-shield-halved text-success me-2"></i> Garantia Legal
                                    </h5>
                                    <div class="list-box list-box-light border-0 mb-4">
                                        <div class="row row-cols-2 row-cols-md-3 g-3">
                                            <div class="col">
                                                <p class="detail-label">Início da Garantia</p>
                                                <p class="detail-value">15/01/2024</p>
                                            </div>
                                            <div class="col">
                                                <p class="detail-label">Fim da Garantia</p>
                                                <p class="detail-value">15/01/2027</p>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">
                                        <i class="fa-solid fa-file-contract text-muted me-2"></i> Contrato de
                                        Manutenção
                                    </h5>
                                    <div class="list-box list-box-light border-0">
                                        <div class="row row-cols-2 row-cols-md-3 g-3">
                                            <div class="col">
                                                <p class="detail-label">Nº Referência</p>
                                                <p class="detail-value custom-monospace" style="font-size: 0.8rem;">
                                                    CNT-2024-001</p>
                                            </div>
                                            <div class="col">
                                                <p class="detail-label">Entidade Responsável</p>
                                                <p class="detail-value">MedServ Técnica</p>
                                            </div>
                                            <div class="col">
                                                <p class="detail-label">Tipo de Contrato</p>
                                                <p class="detail-value">Full-Service</p>
                                            </div>
                                            <div class="col">
                                                <p class="detail-label">Periodicidade</p>
                                                <p class="detail-value">Anual</p>
                                            </div>
                                            <div class="col">
                                                <p class="detail-label">Início do Contrato</p>
                                                <p class="detail-value">15/01/2024</p>
                                            </div>
                                            <div class="col">
                                                <p class="detail-label">Fim do Contrato</p>
                                                <p class="detail-value">15/01/2027</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 5: DOCUMENTOS -->
                                <div class="tab-pane fade" id="documentos-pane" role="tabpanel" tabindex="0">
                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Ficheiros
                                        Anexados</h5>
                                    <div class="d-flex flex-column gap-2 mt-3">

                                        <div
                                            class="list-box list-box-light d-flex justify-content-between align-items-center gap-3">
                                            <div class="flex-grow-1">
                                                <p class="detail-value fw-medium mb-0">IntelliVue MX700 - Manual do
                                                    Utilizador</p>
                                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Manual
                                                    de Utilizador — 01/01/2023</p>
                                                <p class="text-muted custom-monospace small mb-0"
                                                    style="font-size: 0.75rem;">
                                                    <i
                                                        class="fa-solid fa-file-pdf text-danger me-1"></i>intellivue_mx700_manual.pdf
                                                </p>
                                            </div>
                                            <a href="../../assets/docs/intellivue_mx700_manual.pdf" download
                                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0"
                                                title="Descarregar ficheiro">
                                                <i class="fa-solid fa-download"></i>
                                                <span class="d-none d-sm-inline">Download</span>
                                            </a>
                                        </div>

                                        <div class="list-box d-flex justify-content-between align-items-center gap-3"
                                            style="background-color: rgba(254, 242, 242, 0.5); border-color: rgba(254, 202, 202, 0.5);">
                                            <div class="flex-grow-1">
                                                <p class="detail-value fw-medium mb-0">Certificado de Calibração -
                                                    Monitor MX700</p>
                                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                    Certificado de Calibração — 01/06/2024</p>
                                                <p class="text-muted custom-monospace small mb-0"
                                                    style="font-size: 0.75rem;">
                                                    <i
                                                        class="fa-solid fa-file-pdf text-danger me-1"></i>calib_monitor_2024.pdf
                                                </p>
                                                <p class="text-danger fw-medium small mb-0"
                                                    style="font-size: 0.75rem;">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>Validade:
                                                    01/06/2025 (Expirado)
                                                </p>
                                            </div>
                                            <a href="../../assets/docs/calib_monitor_2024.pdf" download
                                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0"
                                                title="Descarregar ficheiro">
                                                <i class="fa-solid fa-download"></i>
                                                <span class="d-none d-sm-inline">Download</span>
                                            </a>
                                        </div>

                                        <div
                                            class="list-box list-box-light d-flex justify-content-between align-items-center gap-3">
                                            <div class="flex-grow-1">
                                                <p class="detail-value fw-medium mb-0">Contrato Full Service Philips
                                                    2024-2027</p>
                                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                    Contrato de Manutenção — 15/01/2024</p>
                                                <p class="text-muted custom-monospace small mb-0"
                                                    style="font-size: 0.75rem;">
                                                    <i
                                                        class="fa-solid fa-file-pdf text-danger me-1"></i>contrato_philips.pdf
                                                </p>
                                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">
                                                    Validade: 15/01/2027</p>
                                            </div>
                                            <a href="../../assets/docs/contrato_philips.pdf" download
                                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0"
                                                title="Descarregar ficheiro">
                                                <i class="fa-solid fa-download"></i>
                                                <span class="d-none d-sm-inline">Download</span>
                                            </a>
                                        </div>

                                    </div>
                                </div>

                                <!-- TAB 6: ACESSÓRIOS -->
                                <div class="tab-pane fade" id="acessorios-pane" role="tabpanel" tabindex="0">
                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Componentes e Acessórios Associados</h5>
                                    <div class="d-flex flex-column gap-2 mt-3">

                                        <div class="list-box list-box-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="detail-value fw-medium">Cabo ECG 5 Vias</p>
                                                <p class="text-brand small fw-bold mb-0" style="font-size: 0.75rem;">EQ-0001.01</p>
                                            </div>
                                            <div class="text-end">
                                                <p class="text-muted custom-monospace small mb-0" style="font-size: 0.75rem;">SN-889900</p>
                                            </div>
                                        </div>

                                        <div class="list-box list-box-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="detail-value fw-medium">Braçadeira de Pressão Adulto</p>
                                                <p class="text-brand small fw-bold mb-0" style="font-size: 0.75rem;">EQ-0001.02</p>
                                            </div>
                                            <div class="text-end">
                                                <p class="text-muted custom-monospace small mb-0 fst-italic" style="font-size: 0.75rem;">Não definido</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Rodapé de Metadados -->
                    <div class="d-flex gap-4 text-muted px-2" style="font-size: 0.75rem;">
                        <span>Criado a: 16/04/2026</span>
                        <span>Última atualização: 16/04/2026</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Equipamento -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg bg-backend">

            <div class="modal-header border-bottom px-4 py-3 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarLabel">Editar Equipamento</h5>
                    <p class="text-muted small mb-0 mt-1">Atualize a informação do equipamento</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body p-4">
                <form id="formEditar" action="#" method="POST" novalidate>

                    <ul class="nav nav-tabs mb-4 border-bottom-0" id="editarTabs" role="tablist"
                        style="pointer-events: none;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark fw-medium" id="edit-step1-tab"
                                data-bs-target="#edit-step1-pane" type="button" role="tab" aria-selected="true">
                                <span class="badge bg-brand text-white me-1 rounded-pill">1</span> Identificação
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-step2-tab"
                                data-bs-target="#edit-step2-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">2</span> Receção e
                                Localização
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-step3-tab"
                                data-bs-target="#edit-step3-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">3</span> Entidades e
                                Contratos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-step4-tab"
                                data-bs-target="#edit-step4-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">4</span> Documentação
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-step5-tab"
                                data-bs-target="#edit-step5-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">5</span> Acessórios
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-step6-tab"
                                data-bs-target="#edit-step6-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">6</span> Observações
                            </button>
                        </li>
                    </ul>

                    <div class="card dash-card mb-0 p-0">
                        <div class="tab-content" id="editarTabsContent">

                            <div class="tab-pane fade show active" id="edit-step1-pane" role="tabpanel"
                                tabindex="0">
                                <div class="card-body p-4">
                                    <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Detalhes de
                                        Identificação</h5>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-medium mb-1">Código Interno <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.7rem;" title="Gerado automaticamente"></i></label>
                                            <input type="text" name="internalCode" class="form-control shadow-sm bg-light fw-bold text-muted" value="EQ-0001" readonly>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-label small fw-medium mb-1">Designação *</label>
                                            <input type="text" name="name" class="form-control shadow-sm"
                                                value="Monitor Multiparamétrico IntelliVue MP5" required>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small fw-medium mb-1">Marca *</label>
                                            <input type="text" name="brand" class="form-control shadow-sm"
                                                value="Philips" required>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-medium mb-1">Modelo *</label>
                                            <input type="text" name="model" class="form-control shadow-sm"
                                                value="MP5" required>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-medium mb-1">Número de Série *</label>
                                            <input type="text" name="serialNumber" class="form-control shadow-sm"
                                                value="SN-PH-2024-001" required>
                                            <div class="invalid-feedback" id="edit-feedbackSerial"
                                                style="font-size: 0.70rem;">Campo obrigatório.</div>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label small fw-medium mb-1">Fabricante *</label>
                                            <div class="dropdown">
                                                <input type="hidden" name="manufacturer" id="edit-inputManufacturer"
                                                    value="Philips Healthcare PT" required>
                                                <button
                                                    class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm bg-white"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                    data-bs-display="static">
                                                    <span id="edit-textManufacturer" class="text-dark">Philips
                                                        Healthcare PT</span>
                                                </button>
                                                <ul
                                                    class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                    <li class="px-2 pb-2 mb-2 border-bottom">
                                                        <input type="text"
                                                            class="form-control form-control-sm shadow-none bg-light"
                                                            id="edit-searchManufacturer" placeholder="Pesquisar..."
                                                            onkeyup="filtrarDropdown('edit-searchManufacturer', 'edit-listaManufacturer')"
                                                            onclick="event.stopPropagation()">
                                                    </li>
                                                    <div id="edit-listaManufacturer">
                                                        <li><a class="dropdown-item py-1 small" href="#"
                                                                onclick="selecionarDropdownEdit('Manufacturer', 'Philips Healthcare PT')">Philips
                                                                Healthcare PT</a></li>
                                                        <li><a class="dropdown-item py-1 small" href="#"
                                                                onclick="selecionarDropdownEdit('Manufacturer', 'Dräger Portugal')">Dräger
                                                                Portugal</a></li>
                                                        <li><a class="dropdown-item py-1 small" href="#"
                                                                onclick="selecionarDropdownEdit('Manufacturer', 'Mindray')">Mindray</a>
                                                        </li>
                                                    </div>
                                                </ul>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                    obrigatório.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-medium mb-1">Ano de Fabrico</label>
                                            <input type="number" name="manufacturingYear"
                                                class="form-control form-control-sm shadow-sm" value="2023"
                                                min="1900">
                                            <div class="invalid-feedback" id="edit-feedbackAno"
                                                style="font-size: 0.70rem;">Data inválida.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-medium mb-1">Categoria *</label>
                                            <div class="dropdown">
                                                <input type="hidden" name="categoria" id="edit-inputCategoria"
                                                    value="Monitorização" required>
                                                <button
                                                    class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                    data-bs-display="static">
                                                    <span id="edit-textCategoria"
                                                        class="text-dark">Monitorização</span>
                                                </button>
                                                <ul
                                                    class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                    <li class="px-2 pb-2 mb-2 border-bottom">
                                                        <input type="text"
                                                            class="form-control form-control-sm shadow-none bg-light"
                                                            id="edit-searchCategoria"
                                                            placeholder="Escreva para pesquisar..."
                                                            onkeyup="filtrarDropdown('edit-searchCategoria', 'edit-listaCategoria')"
                                                            onclick="event.stopPropagation()">
                                                    </li>
                                                    <div id="edit-listaCategoria">
                                                        <li><a class="dropdown-item py-2" href="#"
                                                                onclick="selecionarDropdownEdit('Categoria', 'Monitorização')">Monitorização</a>
                                                        </li>
                                                        <li><a class="dropdown-item py-2" href="#"
                                                                onclick="selecionarDropdownEdit('Categoria', 'Suporte de vida')">Suporte
                                                                de vida</a></li>
                                                    </div>
                                                </ul>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                    obrigatório.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-medium mb-1">Criticidade Clínica
                                                *</label>
                                            <div class="dropdown">
                                                <input type="hidden" name="criticidade" id="edit-inputCriticidade"
                                                    value="Alta" required>
                                                <button
                                                    class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                    data-bs-display="static">
                                                    <span id="edit-textCriticidade" class="text-dark">Alta</span>
                                                </button>
                                                <ul
                                                    class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                    <li><a class="dropdown-item py-2" href="#"
                                                            onclick="selecionarDropdownEdit('Criticidade', 'Baixa')">Baixa</a>
                                                    </li>
                                                    <li><a class="dropdown-item py-2" href="#"
                                                            onclick="selecionarDropdownEdit('Criticidade', 'Média')">Média</a>
                                                    </li>
                                                    <li><a class="dropdown-item py-2" href="#"
                                                            onclick="selecionarDropdownEdit('Criticidade', 'Alta')">Alta</a>
                                                    </li>
                                                    <li><a class="dropdown-item py-2" href="#"
                                                            onclick="selecionarDropdownEdit('Criticidade', 'Suporte de vida')">Suporte
                                                            de vida</a></li>
                                                </ul>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                    obrigatório.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="edit-alertaPasso1"
                                        class="alert alert-danger p-2 text-center mt-4 mb-0 shadow-sm d-none"
                                        role="alert">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Erro:</strong>
                                        Por favor, verifique os campos a vermelho antes de avançar.
                                    </div>
                                </div>
                                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-brand px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step2-tab">Próximo <i
                                            class="fa-solid fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-step2-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-4">
                                    <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Receção &
                                        Localização</h5>
                                    <div class="row g-5">
                                        <div class="col-lg-6 border-end">
                                            <h6 class="fw-bold text-dark mb-4">Entrada e Estado</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium mb-1">Data de
                                                        Aquisição</label>
                                                    <input type="date" name="acquisitionDate"
                                                        class="form-control shadow-sm" value="2024-01-15">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium mb-1">Custo (EUR)
                                                        *</label>
                                                    <input type="number" name="cost" class="form-control shadow-sm"
                                                        value="18500.00" step="0.01" min="0" required>
                                                    <div class="invalid-feedback" id="edit-feedbackCusto"
                                                        style="font-size: 0.70rem;">Campo obrigatório.</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium mb-1">Tipo de Entrada
                                                        *</label>
                                                    <select name="entryType" class="form-select shadow-sm text-dark"
                                                        required>
                                                        <option value="Compra" selected>Compra</option>
                                                        <option value="Doação">Doação</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium mb-1">Estado Atual
                                                        *</label>
                                                    <select name="status"
                                                        class="form-select shadow-sm text-dark border-warning bg-warning bg-opacity-10"
                                                        required>
                                                        <option value="Ativo" selected>Ativo / Operacional</option>
                                                        <option value="Em Manutenção">Em Manutenção</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <h6 class="fw-bold text-dark mb-4">Localização Hierárquica</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium mb-1">Edifício
                                                        *</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="edificio" id="edit-inputEdificio"
                                                            value="Edifício Principal" required>
                                                        <button id="edit-btnEdificio"
                                                            class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false" data-bs-display="static">
                                                            <span id="edit-textEdificio" class="text-dark">Edifício
                                                                Principal</span>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                            <li class="px-2 pb-2 mb-2 border-bottom">
                                                                <input type="text"
                                                                    class="form-control form-control-sm shadow-none bg-light"
                                                                    id="edit-searchEdificio"
                                                                    placeholder="Pesquisar..."
                                                                    onkeyup="filtrarDropdown('edit-searchEdificio', 'edit-listaEdificio')"
                                                                    onclick="event.stopPropagation()">
                                                            </li>
                                                            <div id="edit-listaEdificio">
                                                                <li><a class="dropdown-item py-2" href="#"
                                                                        onclick="selecionarLocalizacaoEdit('Edificio', 'Edifício Principal', 'Piso')">Edifício
                                                                        Principal</a></li>
                                                            </div>
                                                        </ul>
                                                        <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                            Campo obrigatório.</div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium mb-1">Piso *</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="piso" id="edit-inputPiso"
                                                            value="Piso 1" required>
                                                        <button id="edit-btnPiso"
                                                            class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false" data-bs-display="static">
                                                            <span id="edit-textPiso" class="text-dark">Piso 1</span>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                            <div id="edit-listaPiso">
                                                                <li><a class="dropdown-item py-2" href="#"
                                                                        onclick="selecionarLocalizacaoEdit('Piso', 'Piso 1', 'Servico')">Piso
                                                                        1</a></li>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label small fw-medium mb-1">Serviço *</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="servico" id="edit-inputServico"
                                                            value="Unidade de Cuidados Intensivos (UCI)" required>
                                                        <button id="edit-btnServico"
                                                            class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false" data-bs-display="static">
                                                            <span id="edit-textServico" class="text-dark">Unidade de
                                                                Cuidados Intensivos (UCI)</span>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                            <div id="edit-listaServico">
                                                                <li><a class="dropdown-item py-2" href="#"
                                                                        onclick="selecionarLocalizacaoEdit('Servico', 'Unidade de Cuidados Intensivos (UCI)', 'Sala')">Unidade
                                                                        de Cuidados Intensivos (UCI)</a></li>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label small fw-medium mb-1">Sala /
                                                        Compartimento (Opcional)</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="sala" id="edit-inputSala"
                                                            value="Box 1">
                                                        <button id="edit-btnSala"
                                                            class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false" data-bs-display="static">
                                                            <span id="edit-textSala" class="text-dark">Box 1</span>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                            <div id="edit-listaSala">
                                                                <li><a class="dropdown-item py-2" href="#"
                                                                        onclick="selecionarLocalizacaoEdit('Sala', 'Box 1', null)">Box
                                                                        1</a></li>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-danger p-2 text-center mt-4 mb-0 shadow-sm d-none edit-alertaGlobal"
                                        role="alert">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Erro:</strong>
                                        Por favor, verifique os campos a vermelho antes de avançar.
                                    </div>
                                </div>
                                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step1-tab"><i
                                            class="fa-solid fa-arrow-left me-1"></i> Anterior</button>
                                    <button type="button" class="btn btn-brand px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step3-tab">Próximo <i
                                            class="fa-solid fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-step3-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-3 p-md-4">
                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Contratos &
                                        Entidades Associadas</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-3 h-100 bg-light">
                                                <h6 class="fw-bold text-dark mb-3">Entidades</h6>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-medium mb-1">Fornecedor
                                                        Comercial *</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="fornecedor"
                                                            id="edit-inputFornecedor" value="Philips Healthcare PT"
                                                            required>
                                                        <button
                                                            class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <span id="edit-textFornecedor" class="text-dark">Philips
                                                                Healthcare PT</span>
                                                        </button>
                                                        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                                                            <li><a class="dropdown-item py-1 small" href="#"
                                                                    onclick="selecionarDropdownEdit('Fornecedor', 'Philips Healthcare PT')">Philips
                                                                    Healthcare PT</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-medium mb-1">Assistência
                                                        Técnica Oficial *</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="assistencia"
                                                            id="edit-inputAssistencia" value="MedServ Técnica"
                                                            required>
                                                        <button
                                                            class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <span id="edit-textAssistencia"
                                                                class="text-dark">MedServ Técnica</span>
                                                        </button>
                                                        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                                                            <li><a class="dropdown-item py-1 small" href="#"
                                                                    onclick="selecionarDropdownEdit('Assistencia', 'MedServ Técnica')">MedServ
                                                                    Técnica</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label small fw-medium mb-1">Fornecedor de
                                                        Consumíveis (Opcional)</label>
                                                    <div class="dropdown">
                                                        <input type="hidden" name="consumiveis"
                                                            id="edit-inputConsumiveis">
                                                        <button
                                                            class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <span id="edit-textConsumiveis"
                                                                class="text-muted">Selecionar fornecedor...</span>
                                                        </button>
                                                        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                                                            <li><a class="dropdown-item py-1 small" href="#"
                                                                    onclick="selecionarDropdownEdit('Consumiveis', 'FarmaMed Consumíveis')">FarmaMed
                                                                    Consumíveis</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div
                                                class="p-3 border border-success-subtle bg-success bg-opacity-10 rounded-3 h-100">
                                                <h6 class="fw-bold text-dark mb-2">Cobertura Legal</h6>

                                                <div class="mb-2 pb-2 border-bottom border-success-subtle">
                                                    <div class="form-check form-switch mb-1">
                                                        <input class="form-check-input" type="checkbox"
                                                            role="switch" id="edit-temGarantia"
                                                            onchange="toggleCamposEdit('edit-temGarantia', 'edit-camposGarantia')"
                                                            checked>
                                                        <label class="form-check-label fw-bold text-dark small"
                                                            for="edit-temGarantia">Dentro da Garantia Legal</label>
                                                    </div>
                                                    <div id="edit-camposGarantia" class="row g-2">
                                                        <div class="col-6">
                                                            <label
                                                                class="form-label small fw-medium text-dark mb-1">Início
                                                                da Garantia *</label>
                                                            <input type="date" name="garantiaInicio"
                                                                class="form-control shadow-sm form-control-sm bg-white"
                                                                value="2024-01-15" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label
                                                                class="form-label small fw-medium text-dark mb-1">Fim
                                                                da Garantia *</label>
                                                            <input type="date" name="garantiaFim"
                                                                class="form-control shadow-sm form-control-sm border-warning bg-white"
                                                                value="2027-01-15" required>
                                                            <div class="invalid-feedback" id="edit-erroDataGarantia"
                                                                style="font-size: 0.70rem;">Inválido.</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="form-check form-switch mb-1">
                                                        <input class="form-check-input" type="checkbox"
                                                            role="switch" id="edit-temContrato"
                                                            onchange="toggleCamposEdit('edit-temContrato', 'edit-camposContrato')"
                                                            checked>
                                                        <label class="form-check-label fw-bold text-dark small"
                                                            for="edit-temContrato">Possui Contrato de
                                                            Manutenção</label>
                                                    </div>
                                                    <div id="edit-camposContrato" class="row g-2">
                                                        <div class="col-5">
                                                            <label class="form-label small text-dark mb-1">Nº
                                                                Referência *</label>
                                                            <input type="text" name="referenciaContrato"
                                                                class="form-control shadow-sm form-control-sm bg-white"
                                                                value="CNT-2024-883" required>
                                                        </div>
                                                        <div class="col-7">
                                                            <label class="form-label small text-dark mb-1">Entidade
                                                                Responsável *</label>
                                                            <select name="entidadeContrato"
                                                                class="form-select form-select-sm shadow-sm bg-white"
                                                                required>
                                                                <option value="Philips Healthcare PT" selected>
                                                                    Philips Healthcare PT</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <label class="form-label small text-dark mb-1">Tipo de
                                                                Contrato *</label>
                                                            <select name="tipoContrato"
                                                                class="form-select form-select-sm shadow-sm text-dark bg-white"
                                                                required>
                                                                <option value="Full-Service" selected>Full-Service
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <label
                                                                class="form-label small text-dark mb-1">Periodicidade
                                                                *</label>
                                                            <select name="periodicidadeContrato"
                                                                class="form-select form-select-sm shadow-sm text-dark bg-white"
                                                                required>
                                                                <option value="Anual" selected>Anual</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <label class="form-label small text-dark mb-1">Início
                                                                *</label>
                                                            <input type="date" name="contratoInicio"
                                                                class="form-control shadow-sm form-control-sm bg-white"
                                                                value="2024-01-15" required>
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <label class="form-label small text-dark mb-1">Fim
                                                                *</label>
                                                            <input type="date" name="contratoFim"
                                                                class="form-control shadow-sm form-control-sm border-warning bg-white"
                                                                value="2027-01-15" required>
                                                            <div class="invalid-feedback" id="edit-erroDataContrato"
                                                                style="font-size: 0.70rem;">Inválido.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger p-2 text-center mt-4 mb-0 shadow-sm d-none edit-alertaGlobal"
                                        role="alert">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Erro:</strong>
                                        Por favor, verifique os campos a vermelho antes de avançar.
                                    </div>
                                </div>
                                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step2-tab"><i
                                            class="fa-solid fa-arrow-left me-1"></i> Anterior</button>
                                    <button type="button" class="btn btn-brand px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step4-tab">Próximo <i
                                            class="fa-solid fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-step4-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-3 p-md-4">
                                    <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Documentação
                                        Técnica e Legal</h5>

                                    <div
                                        class="mb-4 p-3 border border-warning-subtle bg-warning bg-opacity-10 rounded-3">
                                        <label class="form-label small fw-bold text-dark mb-2">Documentação
                                            Obrigatória em Falta</label>
                                        <div class="d-flex flex-wrap gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input border-warning" type="checkbox"
                                                    id="edit-faltaCE" value="Declaração CE">
                                                <label class="form-check-label small fw-medium text-dark"
                                                    for="edit-faltaCE">Declaração CE</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input border-warning" type="checkbox"
                                                    id="edit-faltaManual" value="Manual Utilizador">
                                                <label class="form-check-label small fw-medium text-dark"
                                                    for="edit-faltaManual">Manual de Utilizador</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input border-warning" type="checkbox"
                                                    id="edit-faltaFatura" value="Fatura">
                                                <label class="form-check-label small fw-medium text-dark"
                                                    for="edit-faltaFatura">Fatura / Guia</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-3 border mb-4">
                                        <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">Anexar Novo
                                            Documento</h6>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-4">
                                                <label class="form-label small text-dark mb-1">Tipo de Documento
                                                    *</label>
                                                <select id="edit-inputTipoDocumento"
                                                    class="form-select form-select-sm shadow-sm bg-white"
                                                    onchange="verificarValidadeDocEdit(this.value)">
                                                    <option value="" selected disabled>Selecionar tipo...</option>
                                                    <option value="Manual de utilizador">Manual de utilizador
                                                    </option>
                                                    <option value="Certificado de calibração">Certificado de
                                                        calibração</option>
                                                    <option value="Contrato de manutenção">Contrato de manutenção
                                                    </option>
                                                    <option value="Fatura ou guia de aquisição">Fatura ou guia de
                                                        aquisição</option>
                                                    <option value="Declaração de conformidade">Declaração de
                                                        conformidade</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-dark mb-1">Título
                                                    (Opcional)</label>
                                                <input type="text" id="edit-docTitulo"
                                                    class="form-control form-control-sm shadow-sm bg-white"
                                                    placeholder="Ex: Manual v2.0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-dark mb-1">Data Emissão</label>
                                                <input type="date" id="edit-docEmissao"
                                                    class="form-control form-control-sm shadow-sm bg-white">
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label small text-dark mb-1">Validade</label>
                                                <input type="date" id="edit-dataValidadeDoc"
                                                    class="form-control form-control-sm shadow-sm" disabled>
                                            </div>
                                            <div class="col-md-7">
                                                <label class="form-label small text-dark mb-1">Ficheiro (PDF, JPG)
                                                    *</label>
                                                <input type="file" id="edit-docFicheiro"
                                                    class="form-control form-control-sm shadow-sm bg-white">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" id="edit-btnAnexarDoc"
                                                    class="btn btn-sm btn-brand fw-bold shadow-sm w-100">
                                                    <i class="fa-solid fa-plus"></i> Anexar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded-3 overflow-hidden shadow-sm">
                                        <div class="table-responsive bg-white"
                                            style="max-height: 180px; overflow-y: auto;">
                                            <table class="table table-hover mb-0 text-start align-middle"
                                                style="position: relative;">
                                                <thead
                                                    class="table-light text-muted small text-uppercase sticky-top"
                                                    style="font-size: 0.75rem; z-index: 1;">
                                                    <tr>
                                                        <th class="fw-semibold px-3 py-2 border-bottom">Documento
                                                        </th>
                                                        <th class="fw-semibold py-2 border-bottom">Validade</th>
                                                        <th class="fw-semibold py-2 border-bottom">Ficheiro</th>
                                                        <th class="fw-semibold text-end px-3 py-2 border-bottom">
                                                            Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="edit-tabelaDocsBody">
                                                    <tr>
                                                        <td class="px-3 py-2 small fw-medium text-dark"><span
                                                                class="badge bg-secondary mb-1 d-inline-block edit-tipo-doc-anexado">Manual
                                                                de utilizador</span><br>Manual Base</td>
                                                        <td class="py-2 small text-muted">N/A</td>
                                                        <td class="py-2 small text-muted"><i
                                                                class="fa-solid fa-file-pdf text-danger me-1"></i>
                                                            manual_v1.pdf</td>
                                                        <td class="text-end px-3 py-2"><button type="button"
                                                                class="btn btn-sm text-danger btn-remover-doc"><i
                                                                    class="fa-solid fa-trash-can"></i></button></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-3 py-2 small fw-medium text-dark"><span
                                                                class="badge bg-secondary mb-1 d-inline-block edit-tipo-doc-anexado">Contrato
                                                                de manutenção</span><br>Contrato Philips</td>
                                                        <td class="py-2 small text-warning fw-bold">2027-01-15</td>
                                                        <td class="py-2 small text-muted"><i
                                                                class="fa-solid fa-file-pdf text-danger me-1"></i>
                                                            contrato.pdf</td>
                                                        <td class="text-end px-3 py-2"><button type="button"
                                                                class="btn btn-sm text-danger btn-remover-doc"><i
                                                                    class="fa-solid fa-trash-can"></i></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="edit-alertaPasso4"
                                        class="alert alert-danger p-2 text-center mt-4 mb-0 shadow-sm d-none"
                                        role="alert">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        <strong>Atenção:</strong> <span id="edit-textoAlertaPasso4"></span>
                                    </div>
                                </div>
                                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step3-tab"><i
                                            class="fa-solid fa-arrow-left me-1"></i> Anterior</button>
                                    <button type="button" class="btn btn-brand px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step5-tab">Próximo <i
                                            class="fa-solid fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-step5-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-4">
                                    <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Acessórios Associados</h5>

                                    <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-medium mb-1">Cód. Componente <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.7rem;" title="Gerado automaticamente"></i></label>
                                            <input type="text" id="edit-acessorioCodigo" class="form-control form-control-sm shadow-sm bg-light fw-bold text-muted" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-medium mb-1">Designação *</label>
                                            <input type="text" id="edit-acessorioDesignacao" class="form-control form-control-sm shadow-sm bg-white" placeholder="Ex: Sensor Oximetria">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-medium mb-1">Nº Série (Opcional)</label>
                                            <input type="text" id="edit-acessorioSerie" class="form-control form-control-sm shadow-sm bg-white">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" id="edit-btnAdicionarAcessorio" class="btn btn-sm btn-brand fw-bold shadow-sm w-100"><i class="fa-solid fa-plus"></i> Adicionar</button>
                                        </div>
                                    </div>

                                    <div class="border rounded-3 overflow-hidden shadow-sm">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="table-light text-muted small text-uppercase" style="font-size: 0.75rem;">
                                                <tr>
                                                    <th class="px-3 py-2">Código</th>
                                                    <th class="py-2">Designação</th>
                                                    <th class="py-2">Referência / Série</th>
                                                    <th class="text-end px-3 py-2">Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody id="edit-tabelaAcessoriosBody">
                                                <tr>
                                                    <td class="px-3 py-2 small fw-bold text-brand">EQ-0001.01</td>
                                                    <td class="py-2 small fw-medium text-dark">Cabo ECG 5 Vias</td>
                                                    <td class="py-2 small text-muted">SN-889900</td>
                                                    <td class="text-end px-3 py-2"><button type="button" class="btn btn-sm text-danger btn-remover-acessorio"><i class="fa-solid fa-trash-can"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td class="px-3 py-2 small fw-bold text-brand">EQ-0001.02</td>
                                                    <td class="py-2 small fw-medium text-dark">Braçadeira de Pressão</td>
                                                    <td class="py-2 small text-muted fst-italic">Não definido</td>
                                                    <td class="text-end px-3 py-2"><button type="button" class="btn btn-sm text-danger btn-remover-acessorio"><i class="fa-solid fa-trash-can"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-edit-wizard" data-bs-wizard-step="#edit-step4-tab"><i class="fa-solid fa-arrow-left me-1"></i> Anterior</button>
                                    <button type="button" class="btn btn-brand px-4 btn-edit-wizard" data-bs-wizard-step="#edit-step6-tab">Próximo <i class="fa-solid fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-step6-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-4">
                                    <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Observações</h5>
                                    <textarea name="observations" class="form-control shadow-sm"
                                        rows="5">Equipamento transferido da Urgência para a UCI em fevereiro.</textarea>
                                </div>
                                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-edit-wizard"
                                        data-bs-wizard-step="#edit-step5-tab"><i
                                            class="fa-solid fa-arrow-left me-1"></i> Anterior</button>
                                    <button type="submit" id="btnGuardarEdicao"
                                        class="btn btn-brand d-inline-flex align-items-center gap-2 px-4 shadow-sm fw-bold">
                                        <i class="fa-solid fa-save"></i> Guardar Alterações
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Remover Equipamento -->
<div class="modal fade" id="modalRemover" tabindex="-1" aria-labelledby="modalRemoverLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header -->
            <div class="modal-header border-0 px-4 pt-4 pb-0">

                <h5 class="modal-title fw-bold text-dark" id="modalRemoverLabel">

                    Remover equipamento?

                </h5>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Fechar">
                </button>

            </div>

            <!-- Body -->
            <div class="modal-body px-4 pt-3 pb-4">

                <p class="text-muted mb-0" style="font-size: 1.05rem; line-height: 1.6;">

                    Esta ação irá remover permanentemente
                    <span class="fw-semibold text-dark">
                        "Monitor Multiparamétrico de Sinais Vitais"
                    </span>
                    (<span class="custom-monospace">EQ-2024-001</span>)
                    do inventário, incluindo todos os documentos e
                    garantias associados.
                </p>
            </div>
            <!-- Footer -->
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger px-4">
                    Remover
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script para permitir que os botões "Próximo" e "Anterior" mudem a aba selecionada no topo
        document.querySelectorAll('[data-bs-wizard-step]').forEach(button => {
            button.addEventListener('click', function() {
                const targetTabId = this.getAttribute('data-bs-wizard-step');
                const targetTabElement = document.querySelector(targetTabId);
                if (targetTabElement) {
                    const tab = new bootstrap.Tab(targetTabElement);
                    tab.show();
                }
            });
        });
    });
</script>

<script>
    // Funções Auxiliares para o modal de Edição (Dropwdowns)
    function selecionarDropdownEdit(campoSufixo, valor) {
        const span = document.getElementById('edit-text' + campoSufixo);
        const input = document.getElementById('edit-input' + campoSufixo);
        if (span) {
            span.innerText = valor;
            span.classList.add('text-dark');
        }
        if (input) input.value = valor;
    }

    function selecionarLocalizacaoEdit(nivelAtual, valor, proximoNivel) {
        selecionarDropdownEdit(nivelAtual, valor);
        if (nivelAtual === 'Edificio') {
            desativarNivelLocalizacaoEdit('Servico', 'Aguardando piso...');
            desativarNivelLocalizacaoEdit('Sala', 'Aguardando serviço...');
        } else if (nivelAtual === 'Piso') {
            desativarNivelLocalizacaoEdit('Sala', 'Aguardando serviço...');
        }
        if (proximoNivel) {
            document.getElementById('edit-btn' + proximoNivel).classList.remove('disabled');
            desativarNivelLocalizacaoEdit(proximoNivel, 'Selecionar...', false);
            const lista = document.getElementById('edit-lista' + proximoNivel);
            const items = lista.getElementsByTagName('li');
            for (let i = 0; i < items.length; i++) {
                if (items[i].getAttribute('data-parent') === valor) items[i].style.display = "";
                else items[i].style.display = "none";
            }
        }
    }

    function desativarNivelLocalizacaoEdit(nivel, placeholder, bloquear = true) {
        const btn = document.getElementById('edit-btn' + nivel);
        const span = document.getElementById('edit-text' + nivel);
        if (bloquear) btn.classList.add('disabled');
        span.innerText = placeholder;
        span.classList.add('text-muted');
        span.classList.remove('text-dark');
        document.getElementById('edit-input' + nivel).value = '';
    }

    function toggleCamposEdit(switchId, containerId) {
        const checkBox = document.getElementById(switchId);
        const container = document.getElementById(containerId);
        if (checkBox.checked) container.classList.remove('d-none');
        else container.classList.add('d-none');
    }

    function verificarValidadeDocEdit(valor) {
        const campo = document.getElementById('edit-dataValidadeDoc');
        if (valor === 'Certificado de calibração') {
            campo.disabled = false;
            campo.classList.add('bg-white', 'border-warning');
        } else {
            campo.disabled = true;
            campo.value = '';
            campo.classList.remove('bg-white', 'border-warning');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Reativar a remoção manual de linhas pré-existentes na tabela (ex: documentos e acessórios já guardados)
        document.querySelectorAll('#modalEditar .btn-remover-doc').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });
        document.querySelectorAll('#modalEditar .btn-remover-acessorio').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });

        // LÓGICA ANEXAR DOC (Edição)
        const btnAnexarDocEdit = document.getElementById('edit-btnAnexarDoc');
        if (btnAnexarDocEdit) {
            btnAnexarDocEdit.addEventListener('click', function() {
                const tipo = document.getElementById('edit-inputTipoDocumento').value;
                const titulo = document.getElementById('edit-docTitulo').value;
                const emissao = document.getElementById('edit-docEmissao').value;
                const validade = document.getElementById('edit-dataValidadeDoc').value;
                const ficheiroInput = document.getElementById('edit-docFicheiro');
                const ficheiro = ficheiroInput.value;

                if (!tipo || !ficheiro) {
                    alert("Selecione o Tipo e escolha um Ficheiro.");
                    return;
                }
                if (document.getElementById('edit-dataValidadeDoc').disabled === false && !validade) {
                    alert("Validade é obrigatória para calibração.");
                    return;
                }
                if (emissao && validade && new Date(validade) < new Date(emissao)) {
                    alert("Validade não pode ser anterior à Emissão.");
                    return;
                }

                const tbody = document.getElementById('edit-tabelaDocsBody');
                const fileName = ficheiro.split('\\').pop();

                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td class="px-3 py-2 small fw-medium text-dark"><span class="badge bg-secondary mb-1 edit-tipo-doc-anexado">${tipo}</span><br>${titulo || 'Documento Base'}</td>
                        <td class="py-2 small ${validade ? 'text-warning fw-bold' : 'text-muted'}">${validade || 'N/A'}</td>
                        <td class="py-2 small text-muted"><i class="fa-solid fa-file-pdf text-danger me-1"></i> ${fileName}</td>
                        <td class="text-end px-3 py-2"><button type="button" class="btn btn-sm text-danger btn-remover-doc"><i class="fa-solid fa-trash-can"></i></button></td>
                    `;
                tbody.appendChild(tr);
                tr.querySelector('.btn-remover-doc').addEventListener('click', () => tr.remove());

                // Limpa formulário
                document.getElementById('edit-inputTipoDocumento').value = '';
                document.getElementById('edit-docTitulo').value = '';
                document.getElementById('edit-docEmissao').value = '';
                document.getElementById('edit-dataValidadeDoc').value = '';
                ficheiroInput.value = '';
            });
        }

        // =========================================================================
        // LÓGICA ANEXAR ACESSÓRIO (Edição) - COM AUTO-INCREMENTO
        // =========================================================================

        function atualizarCodigoEdicaoAcessorio() {
            const tbody = document.getElementById('edit-tabelaAcessoriosBody');
            let numeros = [];

            // 1. Guarda todos os números
            for (let tr of tbody.children) {
                const tdCode = tr.querySelector('td:first-child');
                if (tdCode && tdCode.innerText.includes('.')) {
                    const partes = tdCode.innerText.split('.');
                    if (partes.length === 2) {
                        numeros.push(parseInt(partes[1], 10));
                    }
                }
            }

            // 2. Ordena
            numeros.sort((a, b) => a - b);

            // 3. Procura a primeira lacuna livre
            let proximoNum = 1;
            for (let i = 0; i < numeros.length; i++) {
                if (numeros[i] === proximoNum) {
                    proximoNum++;
                } else if (numeros[i] > proximoNum) {
                    break;
                }
            }

            // 4. Aplica
            const proximoNumStr = proximoNum.toString().padStart(2, '0');

            // Vai buscar o código do equipamento que está a ser editado (Passo 1 do modal)
            const codigoPrincipal = document.querySelector('input[name="internalCode"]').value || 'EQ-0001';

            document.getElementById('edit-acessorioCodigo').value = `${codigoPrincipal}.${proximoNumStr}`;
        }

        const btnAdcAcc = document.getElementById('edit-btnAdicionarAcessorio');
        if (btnAdcAcc) {
            // Calcula logo o próximo número ao abrir (ex: EQ-0001.03)
            atualizarCodigoEdicaoAcessorio();

            // Permitir que os botões de lixo que já vêm no HTML também recalculem o código
            document.querySelectorAll('#edit-tabelaAcessoriosBody .btn-remover-acessorio').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('tr').remove();
                    atualizarCodigoEdicaoAcessorio();
                });
            });

            btnAdcAcc.addEventListener('click', function() {
                const cod = document.getElementById('edit-acessorioCodigo').value;
                const des = document.getElementById('edit-acessorioDesignacao').value.trim();
                const ser = document.getElementById('edit-acessorioSerie').value.trim();

                if (!des) {
                    document.getElementById('edit-acessorioDesignacao').classList.add('is-invalid');
                    return;
                } else {
                    document.getElementById('edit-acessorioDesignacao').classList.remove('is-invalid');
                }

                const tbody = document.getElementById('edit-tabelaAcessoriosBody');
                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td class="px-3 py-2 small fw-bold text-brand">${cod}</td>
                        <td class="py-2 small fw-medium text-dark">${des}</td>
                        <td class="py-2 small text-muted">${ser || 'N/A'}</td>
                        <td class="text-end px-3 py-2"><button type="button" class="btn btn-sm text-danger btn-remover-acessorio"><i class="fa-solid fa-trash-can"></i></button></td>
                    `;
                tbody.appendChild(tr);

                tr.querySelector('.btn-remover-acessorio').addEventListener('click', () => {
                    tr.remove();
                    atualizarCodigoEdicaoAcessorio(); // Recalcula se apagarmos
                });

                // Limpa formulário
                document.getElementById('edit-acessorioDesignacao').value = '';
                document.getElementById('edit-acessorioSerie').value = '';

                // Gera o próximo código imediatamente!
                atualizarCodigoEdicaoAcessorio();
            });
        }

        // MOTOR DE VALIDAÇÃO WIZARD (EDIÇÃO)
        document.querySelectorAll('#modalEditar .btn-edit-wizard').forEach(button => {
            button.addEventListener('click', function() {
                const painelAtual = document.querySelector('#modalEditar .tab-pane.active');
                if (this.innerText.toLowerCase().includes("anterior")) {
                    mudarSeparadorEdit(this.getAttribute('data-bs-wizard-step'));
                    return;
                }

                let tudoValido = true;
                const alertasGerais = painelAtual.querySelectorAll('.edit-alertaGlobal');
                alertasGerais.forEach(a => a.classList.add('d-none'));

                // Reset
                const fAno = document.getElementById('edit-feedbackAno');
                if (fAno) fAno.innerText = "Data inválida.";
                const fCusto = document.getElementById('edit-feedbackCusto');
                if (fCusto) fCusto.innerText = "Campo obrigatório.";

                // Validar Required
                painelAtual.querySelectorAll('input[required], select[required]').forEach(campo => {
                    if (campo.closest('.d-none')) return;
                    let elemento = campo.type === 'hidden' ? campo.nextElementSibling : campo;
                    if (!campo.value || campo.value.trim() === '') {
                        elemento.classList.add('is-invalid', 'border-danger');
                        tudoValido = false;
                    } else {
                        elemento.classList.remove('is-invalid', 'border-danger');
                    }
                });

                // Datas Contrato e Garantia
                const cGar = document.getElementById('edit-camposGarantia');
                if (cGar && !cGar.classList.contains('d-none')) {
                    const dIni = painelAtual.querySelector('input[name="garantiaInicio"]');
                    const dFim = painelAtual.querySelector('input[name="garantiaFim"]');
                    if (dIni && dFim && dIni.value && dFim.value && new Date(dFim.value) <= new Date(dIni.value)) {
                        dFim.classList.add('is-invalid', 'border-danger');
                        document.getElementById('edit-erroDataGarantia').innerText = "Posterior ao início.";
                        tudoValido = false;
                    }
                }
                const cCont = document.getElementById('edit-camposContrato');
                if (cCont && !cCont.classList.contains('d-none')) {
                    const dIni = painelAtual.querySelector('input[name="contratoInicio"]');
                    const dFim = painelAtual.querySelector('input[name="contratoFim"]');
                    if (dIni && dFim && dIni.value && dFim.value && new Date(dFim.value) <= new Date(dIni.value)) {
                        dFim.classList.add('is-invalid', 'border-danger');
                        document.getElementById('edit-erroDataContrato').innerText = "Posterior ao início.";
                        tudoValido = false;
                    }
                }

                // Validação de Documentos (Passo 4)
                if (painelAtual.id === 'edit-step4-pane') {
                    const alerta = document.getElementById('edit-alertaPasso4');
                    const texto = document.getElementById('edit-textoAlertaPasso4');
                    const docsTabela = Array.from(painelAtual.querySelectorAll('.edit-tipo-doc-anexado')).map(span => span.innerText);
                    let msg = "";

                    if (document.getElementById('edit-temContrato').checked && !docsTabela.includes('Contrato de manutenção')) {
                        msg += "Falta o <strong>Contrato de manutenção</strong>.<br>";
                        tudoValido = false;
                    }
                    if (!document.getElementById('edit-faltaCE').checked && !docsTabela.includes('Declaração de conformidade')) {
                        msg += "Falta <strong>Declaração CE</strong> (ou marque em falta).<br>";
                        tudoValido = false;
                    }
                    if (!document.getElementById('edit-faltaManual').checked && !docsTabela.includes('Manual de utilizador')) {
                        msg += "Falta <strong>Manual</strong> (ou marque em falta).<br>";
                        tudoValido = false;
                    }
                    if (!document.getElementById('edit-faltaFatura').checked && !docsTabela.includes('Fatura ou guia de aquisição')) {
                        msg += "Falta <strong>Fatura/Guia</strong> (ou marque em falta).<br>";
                        tudoValido = false;
                    }

                    if (!tudoValido) {
                        texto.innerHTML = msg;
                        alerta.classList.remove('d-none');
                    } else {
                        alerta.classList.add('d-none');
                    }
                }

                // Ano e Custo
                const anoInp = painelAtual.querySelector('input[name="manufacturingYear"]');
                if (anoInp && anoInp.value && (parseInt(anoInp.value) < 1900 || parseInt(anoInp.value) > new Date().getFullYear())) {
                    anoInp.classList.add('is-invalid');
                    tudoValido = false;
                }
                const custoInp = painelAtual.querySelector('input[name="cost"]');
                if (custoInp && custoInp.value && parseFloat(custoInp.value) < 0) {
                    custoInp.classList.add('is-invalid');
                    tudoValido = false;
                }

                // Sucesso
                if (tudoValido) mudarSeparadorEdit(this.getAttribute('data-bs-wizard-step'));
                else alertasGerais.forEach(a => a.classList.remove('d-none'));
            });
        });

        function mudarSeparadorEdit(target) {
            new bootstrap.Tab(document.querySelector(target)).show();
            const tabCerta = target.replace('-pane', '-tab').replace('#', '');
            document.querySelectorAll('#editarTabs .nav-link').forEach(nav => {
                const badge = nav.querySelector('.badge');
                if (nav.id === tabCerta) {
                    nav.classList.replace('text-muted', 'text-dark');
                    nav.classList.add('active');
                    badge.classList.replace('bg-secondary', 'bg-brand');
                } else {
                    nav.classList.replace('text-dark', 'text-muted');
                    nav.classList.remove('active');
                    badge.classList.replace('bg-brand', 'bg-secondary');
                }
            });
        }

        // SUBMETER EDIÇÃO (Passo 6)
        const formEdicao = document.getElementById('formEditar');
        if (formEdicao) {
            formEdicao.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnGuardarEdicao');
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> A guardar...';
                btn.disabled = true;

                setTimeout(() => {
                    alert("✅ Alterações guardadas com sucesso!");
                    window.location.reload();
                }, 1000);
            });
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>