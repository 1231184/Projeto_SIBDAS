<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

redirect_if_not_profile(['Administrador']);

$erros = [];
$erro_sistema = "";
$sucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : 0;

// =======================================================
// FICHA 12: INSERIR NOVO FORNECEDOR (MÉTODO POST)
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'novo_fornecedor') {

    // 1. Recolher Dados (Mapeando o HTML para os nomes da tua BD)
    $nome_empresa         = trim($_POST["nome_empresa"] ?? "");
    $nif                  = trim($_POST["nif"] ?? "");
    $tipo_fornecedor      = trim($_POST["tipo_fornecedor"] ?? ""); // Tem de ser igualzinho ao CHECK do MySQL
    $morada               = trim($_POST["morada"] ?? "");
    $website              = trim($_POST["website"] ?? "");
    $telefone_geral       = trim($_POST["telefone_geral"] ?? "");
    $email_geral          = trim($_POST["email_geral"] ?? "");

    $nome_responsavel     = trim($_POST["nome_responsavel"] ?? "");
    // Lemos os campos "diretos" do HTML para guardar nas colunas "responsavel" da BD
    $telefone_responsavel = trim($_POST["telefone_direto"] ?? "");
    $email_responsavel    = trim($_POST["email_direto"] ?? "");
    $observacoes          = trim($_POST["observacoes"] ?? "");

    // 2. Validações Básicas
    if (empty($nome_empresa))    $erros["nome_empresa"] = "O nome da empresa é obrigatório.";
    if (empty($telefone_geral))  $erros["telefone_geral"] = "O telefone geral é obrigatório.";
    if (empty($email_geral))     $erros["email_geral"] = "O email geral é obrigatório.";

    // Validação de Segurança do Tipo de Fornecedor (evita o erro da BD)
    $tipos_permitidos = ['Fabricante', 'Distribuidor', 'Assistência Técnica', 'Consumíveis'];
    if (empty($tipo_fornecedor) || !in_array($tipo_fornecedor, $tipos_permitidos)) {
        $erros["tipo_fornecedor"] = "Selecione um tipo de fornecedor válido da lista.";
    }

    // Novas Validações: Morada e Website Obrigatórios
    if (empty($morada))  $erros["morada"] = "A morada fiscal é obrigatória.";
    if (!empty($website) && !preg_match('/^(https?:\/\/)?([\w\-]+(\.[\w\-]+)+)([\/\w\-]*)*\/?$/i', $website)) {
        $erros["website"] = "Introduza um URL válido (ex: www.empresa.pt).";
    }

    // Validação de Formatos
    // Validação de Formatos
    if (empty($nif)) {
        $erros["nif"] = "O NIF é obrigatório.";
    } elseif (!preg_match('/^[0-9]{9}$/', $nif)) {
        $erros["nif"] = "O NIF deve conter 9 dígitos numéricos.";
    }
    if (!empty($telefone_geral) && !preg_match('/^[0-9]{9}$/', $telefone_geral)) {
        $erros["telefone_geral"] = "Introduza 9 dígitos numéricos.";
    }
    if (!empty($email_geral) && !filter_var($email_geral, FILTER_VALIDATE_EMAIL)) {
        $erros["email_geral"] = "Introduza um email válido.";
    }

    // Validação Condicional: Pessoa de Contacto (Como pediste!)
    if (!empty($nome_responsavel)) {
        if (empty($telefone_responsavel)) {
            $erros["telefone_direto"] = "Preencha o telefone se indicou um responsável.";
        } elseif (!preg_match('/^[0-9]{9}$/', $telefone_responsavel)) {
            $erros["telefone_direto"] = "Introduza 9 dígitos numéricos.";
        }

        if (empty($email_responsavel)) {
            $erros["email_direto"] = "Preencha o email se indicou um responsável.";
        } elseif (!filter_var($email_responsavel, FILTER_VALIDATE_EMAIL)) {
            $erros["email_direto"] = "Introduza um email válido.";
        }
    } else {
        // Se não tem nome, mas preencheu algo, validamos o formato na mesma
        if (!empty($telefone_responsavel) && !preg_match('/^[0-9]{9}$/', $telefone_responsavel)) {
            $erros["telefone_direto"] = "Introduza 9 dígitos numéricos.";
        }
        if (!empty($email_responsavel) && !filter_var($email_responsavel, FILTER_VALIDATE_EMAIL)) {
            $erros["email_direto"] = "Introduza um email válido.";
        }
    }

    // 3. Inserir na BD (Mapeado exatamente com a tua tabela fornecedores)
    if (empty($erros)) {
        try {
            $ligacao = new PDO("mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO fornecedores (
                        nome_empresa, nif, tipo_fornecedor, telefone_geral, email_geral, 
                        morada, website, nome_responsavel, telefone_responsavel, email_responsavel, observacoes
                    ) VALUES (
                        :nome, :nif, :tipo, :tel_geral, :email_geral, 
                        :morada, :site, :nome_resp, :tel_resp, :email_resp, :obs
                    )";

            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':nome'        => $nome_empresa,
                ':nif'         => !empty($nif) ? $nif : null,
                ':tipo'        => $tipo_fornecedor,
                ':tel_geral'   => $telefone_geral,
                ':email_geral' => $email_geral,
                ':morada'      => $morada,
                ':site'        => $website,
                ':nome_resp'   => !empty($nome_responsavel) ? $nome_responsavel : null,
                ':tel_resp'    => !empty($telefone_responsavel) ? $telefone_responsavel : null,
                ':email_resp'  => !empty($email_responsavel) ? $email_responsavel : null,
                ':obs'         => !empty($observacoes) ? $observacoes : null
            ]);

            header("Location: lista_fornecedores.php?sucesso=1");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao guardar na Base de Dados: " . $e->getMessage();
        }
    }
}

// =======================================================
// FICHA 13: ATUALIZAR FORNECEDOR (MÉTODO POST)
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'editar_fornecedor') {

    $id_editar            = (int)($_POST['id_fornecedor'] ?? 0);
    $nome_empresa         = trim($_POST["edit_nome_empresa"] ?? "");
    $nif                  = trim($_POST["edit_nif"] ?? "");
    $tipo_fornecedor      = trim($_POST["edit_tipo_fornecedor"] ?? "");
    $morada               = trim($_POST["edit_morada"] ?? "");
    $website              = trim($_POST["edit_website"] ?? "");
    $telefone_geral       = trim($_POST["edit_telefone_geral"] ?? "");
    $email_geral          = trim($_POST["edit_email_geral"] ?? "");
    $nome_responsavel     = trim($_POST["edit_nome_responsavel"] ?? "");
    $telefone_responsavel = trim($_POST["edit_telefone_responsavel"] ?? "");
    $email_responsavel    = trim($_POST["edit_email_responsavel"] ?? "");
    $observacoes          = trim($_POST["edit_observacoes"] ?? "");

    $erros_editar = [];
    if (empty($nome_empresa))   $erros_editar[] = "O nome da empresa é obrigatório.";
    if (empty($telefone_geral)) $erros_editar[] = "O telefone geral é obrigatório.";
    if (empty($email_geral))    $erros_editar[] = "O email geral é obrigatório.";

    $tipos_permitidos = ['Fabricante', 'Distribuidor', 'Assistência Técnica', 'Consumíveis'];
    if (empty($tipo_fornecedor) || !in_array($tipo_fornecedor, $tipos_permitidos)) {
        $erros_editar[] = "Selecione um tipo de fornecedor válido.";
    }
    if (!empty($nif) && !preg_match('/^[0-9]{9}$/', $nif)) {
        $erros_editar[] = "O NIF deve conter 9 dígitos numéricos.";
    }
    if (!empty($telefone_geral) && !preg_match('/^[0-9]{9}$/', $telefone_geral)) {
        $erros_editar[] = "Introduza 9 dígitos numéricos no telefone geral.";
    }
    if (!empty($email_geral) && !filter_var($email_geral, FILTER_VALIDATE_EMAIL)) {
        $erros_editar[] = "Introduza um email geral válido.";
    }

    if (empty($erros_editar) && $id_editar > 0) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE fornecedores SET
                        nome_empresa         = :nome,
                        nif                  = :nif,
                        tipo_fornecedor      = :tipo,
                        telefone_geral       = :tel_geral,
                        email_geral          = :email_geral,
                        morada               = :morada,
                        website              = :site,
                        nome_responsavel     = :nome_resp,
                        telefone_responsavel = :tel_resp,
                        email_responsavel    = :email_resp,
                        observacoes          = :obs
                    WHERE id_fornecedor = :id";

            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':nome'        => $nome_empresa,
                ':nif'         => !empty($nif) ? $nif : null,
                ':tipo'        => $tipo_fornecedor,
                ':tel_geral'   => $telefone_geral,
                ':email_geral' => $email_geral,
                ':morada'      => !empty($morada) ? $morada : null,
                ':site'        => !empty($website) ? $website : null,
                ':nome_resp'   => !empty($nome_responsavel) ? $nome_responsavel : null,
                ':tel_resp'    => !empty($telefone_responsavel) ? $telefone_responsavel : null,
                ':email_resp'  => !empty($email_responsavel) ? $email_responsavel : null,
                ':obs'         => !empty($observacoes) ? $observacoes : null,
                ':id'          => $id_editar
            ]);

            $ligacao = null;
            header("Location: lista_fornecedores.php?sucesso=2");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao atualizar: " . $e->getMessage();
        }
    }
}

// =======================================================
// FICHA 13: REMOVER FORNECEDOR (MÉTODO POST)
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'remover_fornecedor') {

    $id_remover = (int)($_POST['id_fornecedor'] ?? 0);

    if ($id_remover > 0) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $ligacao->prepare("DELETE FROM fornecedores WHERE id_fornecedor = :id");
            $stmt->bindParam(':id', $id_remover, PDO::PARAM_INT);
            $stmt->execute();

            $ligacao = null;
            header("Location: lista_fornecedores.php?sucesso=3");
            exit;
        } catch (PDOException $e) {
            // Código 23000 = violação de FK (fornecedor tem equipamentos associados)
            if ($e->getCode() == 23000) {
                $erro_sistema = "Não é possível remover este fornecedor porque tem equipamentos associados.";
            } else {
                $erro_sistema = "Erro ao remover: " . $e->getMessage();
            }
        }
    }
}

// =======================================================
// FICHA 11: LISTAR FORNECEDORES (SELECT + COUNT)
// =======================================================
try {
    $ligacao = new PDO("mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Agora o JOIN usa o "id_equipamento" e o nome exato "equipamento_fornecedor"
    $sql = "SELECT f.*,
            (
                SELECT COUNT(*) 
                FROM equipamento_fornecedor ef 
                WHERE ef.id_fornecedor = f.id_fornecedor
            )
            +
            (
                SELECT COUNT(*) 
                FROM equipamentos e 
                WHERE e.id_fabricante = f.id_fornecedor
            ) AS total_equipamentos
        FROM fornecedores f
        ORDER BY f.nome_empresa ASC";

    $stmt = $ligacao->query($sql);
    $lista_fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_sistema = "Erro ao carregar lista de Fornecedores: " . $e->getMessage();
    $lista_fornecedores = [];
}
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

    <div id="view-lista" class="fornecedor-view">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Fornecedores</h1>
                <p class="text-muted small mb-0">8 fornecedores registados</p>
            </div>
            <button class="btn btn-brand d-inline-flex align-items-center gap-2 shadow-sm fw-bold"
                data-bs-toggle="modal" data-bs-target="#modalNovoFornecedor">
                <i class="fa-solid fa-plus fs-6"></i> Novo Fornecedor
            </button>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
            <div class="position-relative flex-grow-1" style="max-width: 350px;">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted"
                    style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" id="pesquisaFornecedores" class="form-control ps-5 shadow-sm border-0"
                    placeholder="Pesquisar por nome ou contacto..."
                    style="border-radius: 9px; padding-top: 10px; padding-bottom: 10px;">
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn-filter active">Todos</button>
                <button class="btn-filter">Fabricante</button>
                <button class="btn-filter">Assistência Técnica</button>
                <button class="btn-filter">Distribuidor</button>
                <button class="btn-filter">Consumíveis</button>
            </div>
        </div>

        <div class="card dash-card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table id="tabelaDados" class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Fornecedor</th>
                            <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tipo</th>
                            <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pessoa de Contacto</th>
                            <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Contacto</th>
                            <th class="px-3 py-3 text-muted text-uppercase text-center" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipamentos</th>
                            <th class="px-4 py-3 text-muted text-uppercase text-end" style="font-size: 0.7rem; letter-spacing: 0.5px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lista_fornecedores)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Ainda não existem fornecedores registados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lista_fornecedores as $forn): ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                <div class="fw-bold text-dark small mb-0"><?= htmlspecialchars($forn['nome_empresa']) ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;">NIF: <?= htmlspecialchars($forn['nif'] ?: 'N/D') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <?php
                                        $badge = 'badge-fabricante'; // Cor base (Fabricante)
                                        if ($forn['tipo_fornecedor'] == 'Assistência Técnica') $badge = 'badge-assistencia';
                                        if ($forn['tipo_fornecedor'] == 'Distribuidor') $badge = 'badge-distribuidor';
                                        if ($forn['tipo_fornecedor'] == 'Consumíveis') $badge = 'badge-consumiveis';
                                        ?>
                                        <span class="badge-tipo <?= $badge ?>"><span class="dot"></span><?= htmlspecialchars($forn['tipo_fornecedor']) ?></span>
                                    </td>
                                    <td class="px-3 py-3 fw-medium text-dark small"><?= htmlspecialchars($forn['nome_responsavel'] ?: 'N/D') ?></td>
                                    <td class="px-3 py-3">
                                        <div class="text-dark fw-medium small"><?= htmlspecialchars($forn['telefone_geral']) ?></div>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($forn['email_geral']) ?></div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="badge bg-brand-subtle text-brand fw-bold px-2 py-1 fs-6">
                                            <?= htmlspecialchars($forn['total_equipamentos']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-brand-subtle text-brand fw-semibold shadow-none btn-ver-forn" data-id="<?= $forn['id_fornecedor'] ?>">Ver</button>

                                            <button class="btn btn-sm btn-light border text-danger shadow-none btn-remover-forn" data-id="<?= $forn['id_fornecedor'] ?>"><i class="fa-solid fa-trash-can"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- MODAL Ver Detalhes FORNECEDOR-->
<div class="modal fade" id="modalDetalheFornecedor" tabindex="-1" aria-labelledby="modalDetalheFornecedorLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-backend">

            <!-- HEADER DO MODAL -->
            <div class="modal-header border-bottom px-4 py-3 bg-white">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <!-- Ficha 13: preenchido pelo JS com os dados da BD -->
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalDetalheFornecedorLabel">
                            <span id="detalhe-nomeEmpresa">—</span>
                        </h5>
                        <span id="detalhe-badgeTipo" class="badge-tipo badge-fabricante border">
                            <span class="dot"></span><span id="detalhe-tipoTexto">—</span>
                        </span>
                    </div>
                    <p class="text-muted small mb-0">Ficha de Fornecedor</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Botões de ação: passam o data-id pelo JS antes de abrir o modal -->
                    <button id="detalhe-btnEditar" class="btn-action-custom py-2 px-3" data-id="">
                        <i class="fa-solid fa-pencil me-2"></i> Editar
                    </button>
                    <button id="detalhe-btnRemover" class="btn-action-custom btn-action-danger" data-id="" data-equipamentos="0">
                        <i class="fa-solid fa-trash-can me-2"></i> Remover
                    </button>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
            </div>

            <!-- BODY DO MODAL -->
            <div class="modal-body p-4">
                <div class="w-100" style="max-width: 1024px; margin: 0 auto;">

                    <div class="row g-4 mb-4">

                        <!-- COLUNA ESQUERDA (Info Fixa) -->
                        <div class="col-lg-4 d-flex flex-column gap-3">

                            <!-- Informação Geral -->
                            <div class="card dash-card border-0 shadow-sm p-3">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fa-solid fa-building me-2 text-brand"></i> Informação Geral
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6 d-flex gap-2">
                                        <i class="fa-regular fa-id-card text-muted mt-1" style="font-size: 0.85rem;"></i>
                                        <div>
                                            <div class="small text-muted fw-semibold text-uppercase lh-1" style="font-size: 0.65rem;">NIF</div>
                                            <div class="text-dark fw-medium" style="font-size: 0.80rem;" id="detalhe-nif">—</div>
                                        </div>
                                    </div>
                                    <div class="col-6 d-flex gap-2">
                                        <i class="fa-solid fa-phone text-muted mt-1" style="font-size: 0.85rem;"></i>
                                        <div>
                                            <div class="small text-muted fw-semibold text-uppercase lh-1" style="font-size: 0.65rem;">Telefone Geral</div>
                                            <div class="text-dark fw-medium" style="font-size: 0.80rem;" id="detalhe-telefoneGeral">—</div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex gap-2 mt-2">
                                        <i class="fa-solid fa-envelope text-muted mt-1" style="font-size: 0.85rem;"></i>
                                        <div class="text-truncate">
                                            <div class="small text-muted fw-semibold text-uppercase lh-1" style="font-size: 0.65rem;">Email Geral</div>
                                            <div class="text-dark fw-medium" style="font-size: 0.80rem;" id="detalhe-emailGeral">—</div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex gap-2 mt-2">
                                        <i class="fa-solid fa-location-dot text-muted mt-1" style="font-size: 0.85rem;"></i>
                                        <div>
                                            <div class="small text-muted fw-semibold text-uppercase lh-1" style="font-size: 0.65rem;">Morada</div>
                                            <div class="text-dark fw-medium" style="font-size: 0.80rem;" id="detalhe-morada">—</div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex gap-2 mt-2">
                                        <i class="fa-solid fa-globe text-muted mt-1" style="font-size: 0.85rem;"></i>
                                        <div class="text-truncate">
                                            <div class="small text-muted fw-semibold text-uppercase lh-1" style="font-size: 0.65rem;">Website</div>
                                            <!-- É um <a> para ser clicável, o JS define o href -->
                                            <a id="detalhe-websiteLink" href="#" target="_blank"
                                                class="text-brand fw-medium text-decoration-none" style="font-size: 0.80rem;">—</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pessoa de Contacto (card ocultado pelo JS se não houver responsável) -->
                            <div id="detalhe-secaoResponsavel" class="card dash-card border-0 shadow-sm p-3 d-none">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fa-solid fa-user-tie me-2 text-brand"></i> Gestor de Conta
                                </h6>
                                <div class="d-flex align-items-center gap-3 bg-light rounded-3 p-2 mb-2 border">
                                    <!-- Inicial do nome, gerada pelo JS -->
                                    <div id="detalhe-avatarInicial"
                                        class="rounded-circle d-flex align-items-center justify-content-center bg-brand text-white fw-bold flex-shrink-0"
                                        style="width: 38px; height: 38px; font-size: 0.9rem;">?</div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.85rem;" id="detalhe-nomeResponsavel">—</div>
                                        <div class="text-muted mt-1" style="font-size: 0.75rem;">Responsável</div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2 px-1">
                                    <div class="d-flex gap-2 align-items-center">
                                        <i class="fa-solid fa-phone text-muted" style="font-size: 0.85rem;"></i>
                                        <div class="text-dark fw-medium" style="font-size: 0.80rem;" id="detalhe-telefoneResponsavel">—</div>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <i class="fa-solid fa-envelope text-muted" style="font-size: 0.85rem;"></i>
                                        <div class="text-dark fw-medium text-truncate" style="font-size: 0.80rem;" id="detalhe-emailResponsavel">—</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- COLUNA DIREITA (Notas e Tabela de Equipamentos) -->
                        <div class="col-lg-8 d-flex flex-column gap-4">

                            <!-- Notas -->
                            <div class="card dash-card border-0 shadow-sm p-4">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fa-solid fa-file-lines me-2 text-brand"></i> Notas e Observações
                                </h6>
                                <p class="text-muted small mb-0 lh-lg" id="detalhe-observacoes">—</p>
                            </div>

                            <!-- Tabela de Equipamentos -->
                            <div class="d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fa-solid fa-box-open me-2 text-brand"></i> Equipamentos Associados
                                    <!-- Ficha 13: badge com o total, preenchido pelo JS -->
                                    <span id="detalhe-totalEquipamentos" class="badge bg-brand-subtle text-brand ms-2">0</span>
                                </h6>
                                <div class="card dash-card border-0 shadow-sm overflow-hidden flex-grow-1">
                                    <div class="table-responsive bg-white" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-hover mb-0 align-middle" style="position: relative;">
                                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                                <tr>
                                                    <th class="px-4 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Código</th>
                                                    <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Equipamento</th>
                                                    <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Localização</th>
                                                    <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Relação</th>
                                                    <th class="px-3 py-3 text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Estado</th>
                                                    <th class="px-3 py-3 text-muted text-uppercase text-end" style="font-size: 0.7rem; letter-spacing: 0.5px;"></th>
                                                </tr>
                                            </thead>
                                            <!-- Ficha 13: tbody preenchido dinamicamente pelo JS -->
                                            <tbody id="detalhe-tbodyEquipamentos">
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted small">
                                                        <i class="fa-solid fa-spinner fa-spin me-2"></i> A carregar...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NOVO FORNECEDOR -->
<div class="modal fade" id="modalNovoFornecedor" tabindex="-1" aria-labelledby="modalNovoFornecedorLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-backend">

            <div class="modal-header border-bottom px-4 py-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNovoFornecedorLabel">Novo Fornecedor
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Registe uma nova entidade no sistema</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            <div class="modal-body p-4">
                <form id="formNovoFornecedor" method="POST" action="lista_fornecedores.php" novalidate>
                    <input type="hidden" name="acao" value="novo_fornecedor">

                    <ul class="nav nav-tabs mb-4 border-bottom-0" id="novoFornecedorTabs" role="tablist" style="pointer-events: none;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark fw-medium" id="nf-step1-tab" data-bs-target="#nf-step1-pane" type="button" role="tab" aria-selected="true">
                                <span class="badge bg-brand text-white me-1 rounded-pill">1</span> Empresa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="nf-step2-tab" data-bs-target="#nf-step2-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">2</span> Contactos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="nf-step3-tab" data-bs-target="#nf-step3-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">3</span> Observações
                            </button>
                        </li>
                    </ul>

                    <div class="card dash-card mb-0 p-0 border-0 shadow-none">
                        <div class="tab-content" id="novoFornecedorTabsContent">

                            <div class="tab-pane fade show active" id="nf-step1-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-0 pb-4">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fw-medium small mb-1">Nome da Empresa *</label>
                                            <input type="text" name="nome_empresa" class="form-control shadow-sm bg-white <?= isset($erros['nome_empresa']) ? 'is-invalid' : '' ?>" placeholder="Ex: Philips Healthcare Portugal" value="<?= htmlspecialchars($_POST['nome_empresa'] ?? '') ?>" required>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['nome_empresa'] ?? 'Campo obrigatório.' ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small mb-1">NIF *</label>
                                            <input type="text" id="nifFornecedor" name="nif" required class="form-control shadow-sm bg-white <?= isset($erros['nif']) ? 'is-invalid' : '' ?>" placeholder="Ex: 500123456" value="<?= htmlspecialchars($_POST['nif'] ?? '') ?>">
                                            <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['nif'] ?? 'O NIF é obrigatório (9 dígitos numéricos).' ?></div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium small mb-1">Tipo de Fornecedor *</label>
                                            <div class="dropdown">
                                                <?php $tipoEsc = $_POST['tipo_fornecedor'] ?? ''; ?>
                                                <input type="hidden" name="tipo_fornecedor" id="inputTipoFornecedor" value="<?= htmlspecialchars($tipoEsc) ?>" required>
                                                <button class="form-select shadow-sm text-start d-flex justify-content-between align-items-center bg-white <?= isset($erros['tipo_fornecedor']) ? 'is-invalid border-danger' : '' ?>" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                                                    <span id="textTipoFornecedor" class="<?= !empty($tipoEsc) ? 'text-dark' : 'text-muted' ?>">
                                                        <?= !empty($tipoEsc) ? htmlspecialchars($tipoEsc) : 'Selecionar tipo...' ?>
                                                    </span>
                                                </button>
                                                <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownFornecedor('TipoFornecedor', 'Fabricante')">Fabricante</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownFornecedor('TipoFornecedor', 'Assistência Técnica')">Assistência Técnica</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownFornecedor('TipoFornecedor', 'Distribuidor')">Distribuidor</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownFornecedor('TipoFornecedor', 'Consumíveis')">Consumíveis</a></li>
                                                </ul>
                                            </div>
                                            <div id="erroTipoFornecedor" class="text-danger mt-1 <?= isset($erros['tipo_fornecedor']) ? '' : 'd-none' ?>" style="font-size: 0.70rem;"><?= $erros['tipo_fornecedor'] ?? 'Campo obrigatório.' ?></div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <label class="form-label fw-medium small mb-1">Morada Fiscal / Sede *</label>
                                            <input type="text" name="morada" class="form-control shadow-sm bg-white <?= isset($erros['morada']) ? 'is-invalid' : '' ?>" placeholder="Ex: Rua Principal 1, 1000-001 Lisboa" value="<?= htmlspecialchars($_POST['morada'] ?? '') ?>" required>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['morada'] ?? 'A morada fiscal é obrigatória.' ?></div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium small mb-1">Website</label>
                                            <input type="text" name="website" class="form-control shadow-sm bg-white <?= isset($erros['website']) ? 'is-invalid' : '' ?>" placeholder="Ex: www.empresa.pt" value="<?= htmlspecialchars($_POST['website'] ?? '') ?>">
                                            <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['website'] ?? 'Introduza um URL válido (ex: www.empresa.pt).' ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 border-top d-flex justify-content-between mx-n4 mb-n4 bg-light rounded-bottom">
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-brand px-4 btn-wizard-nf" data-bs-wizard-step="#nf-step2-tab">
                                        Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nf-step2-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-0 pb-4">
                                    <div class="bg-light border rounded-3 p-3 mb-4">
                                        <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2"><i class="fa-solid fa-building me-1 text-brand"></i> Contactos Gerais da Empresa</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small mb-1 text-dark">Telefone Geral *</label>
                                                <input type="tel" name="telefone_geral" class="form-control shadow-sm bg-white <?= isset($erros['telefone_geral']) ? 'is-invalid' : '' ?>" placeholder="Ex: 963 070 470" value="<?= htmlspecialchars($_POST['telefone_geral'] ?? '') ?>" required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['telefone_geral'] ?? 'Introduza 9 dígitos numéricos válidos.' ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small mb-1 text-dark">Email Geral *</label>
                                                <input type="email" name="email_geral" class="form-control shadow-sm bg-white <?= isset($erros['email_geral']) ? 'is-invalid' : '' ?>" placeholder="Ex: geral@empresa.pt" value="<?= htmlspecialchars($_POST['email_geral'] ?? '') ?>" required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['email_geral'] ?? 'Introduza um email válido.' ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light border rounded-3 p-3">
                                        <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2"><i class="fa-solid fa-user-tie me-1 text-brand"></i> Pessoa de Contacto (Gestor de Conta)</h6>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-medium small mb-1 text-muted">Nome do Responsável</label>
                                                <input type="text" name="nome_responsavel" class="form-control shadow-sm bg-white" placeholder="Ex: João Silva" value="<?= htmlspecialchars($_POST['nome_responsavel'] ?? '') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium small mb-1 text-muted">Telefone Direto / Telemóvel</label>
                                                <input type="tel" name="telefone_direto" class="form-control shadow-sm bg-white <?= isset($erros['telefone_direto']) ? 'is-invalid' : '' ?>" placeholder="Ex: 910 000 000" value="<?= htmlspecialchars($_POST['telefone_direto'] ?? '') ?>">
                                                <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['telefone_direto'] ?? 'Introduza 9 dígitos (obrigatório se indicou um responsável).' ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium small mb-1 text-muted">Email Direto</label>
                                                <input type="email" name="email_direto" class="form-control shadow-sm bg-white <?= isset($erros['email_direto']) ? 'is-invalid' : '' ?>" placeholder="Ex: joao.silva@empresa.pt" value="<?= htmlspecialchars($_POST['email_direto'] ?? '') ?>">
                                                <div class="invalid-feedback" style="font-size: 0.70rem;"><?= $erros['email_direto'] ?? 'Introduza um email válido (obrigatório se indicou um responsável).' ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 border-top d-flex justify-content-between mx-n4 mb-n4 bg-light rounded-bottom">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-wizard-nf" data-bs-wizard-step="#nf-step1-tab">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-brand px-4 btn-wizard-nf" data-bs-wizard-step="#nf-step3-tab">
                                        Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nf-step3-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-0 pb-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small mb-1">Notas / Observações</label>
                                        <textarea name="observacoes" class="form-control shadow-sm bg-white" rows="6" placeholder="Observações adicionais, acordos comerciais, prazos de pagamento, etc..."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="p-3 border-top d-flex justify-content-between mx-n4 mb-n4 bg-light rounded-bottom">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-wizard-nf" data-bs-wizard-step="#nf-step2-tab">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                                    </button>
                                    <button type="submit" id="btnCriarFornecedor" class="btn btn-brand px-4 shadow-sm fw-bold">
                                        <i class="fa-solid fa-save me-1"></i> Criar Fornecedor
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

<!-- MODAL EDITAR FORNECEDOR -->
<div class="modal fade" id="modalEditarFornecedor" tabindex="-1" aria-labelledby="modalEditarFornecedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-backend">

            <div class="modal-header border-bottom px-4 py-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand" style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-pen"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarFornecedorLabel">Editar Fornecedor</h5>
                        <p class="text-muted small mb-0 mt-1">Atualize os dados da entidade selecionada</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body p-4">
                <form id="formEditarFornecedor" action="lista_fornecedores.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="editar_fornecedor">
                    <input type="hidden" name="id_fornecedor" id="edit-idFornecedor" value="">

                    <ul class="nav nav-tabs mb-4 border-bottom-0" id="editarFornecedorTabs" role="tablist" style="pointer-events: none;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark fw-medium" id="edit-nf-step1-tab" data-bs-target="#edit-nf-step1-pane" type="button" role="tab" aria-selected="true">
                                <span class="badge bg-brand text-white me-1 rounded-pill">1</span> Empresa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-nf-step2-tab" data-bs-target="#edit-nf-step2-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">2</span> Contactos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-muted fw-medium" id="edit-nf-step3-tab" data-bs-target="#edit-nf-step3-pane" type="button" role="tab" aria-selected="false">
                                <span class="badge bg-secondary text-white me-1 rounded-pill">3</span> Observações
                            </button>
                        </li>
                    </ul>

                    <div class="card dash-card mb-0 p-0 border-0 shadow-none">
                        <div class="tab-content">

                            <div class="tab-pane fade show active" id="edit-nf-step1-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-0 pb-4">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fw-medium small mb-1">Nome da Empresa *</label>
                                            <input type="text" id="edit-nomeEmpresa" name="edit_nome_empresa" class="form-control shadow-sm bg-white" value="" required>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small mb-1">NIF</label>
                                            <input type="text" id="edit-nifFornecedor" name="edit_nif" class="form-control shadow-sm bg-white" value="">
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Introduza 9 dígitos numéricos.</div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium small mb-1">Tipo de Fornecedor *</label>
                                            <div class="dropdown">
                                                <input type="hidden" id="edit-inputTipoFornecedor" name="edit_tipo_fornecedor" value="" required>
                                                <button class="form-select shadow-sm text-start d-flex justify-content-between align-items-center bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                                                    <span id="edit-textTipoFornecedor" class="text-muted">Selecionar tipo...</span>
                                                </button>
                                                <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownEditFornecedor('TipoFornecedor', 'Fabricante')">Fabricante</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownEditFornecedor('TipoFornecedor', 'Assistência Técnica')">Assistência Técnica</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownEditFornecedor('TipoFornecedor', 'Distribuidor')">Distribuidor</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" onclick="selecionarDropdownEditFornecedor('TipoFornecedor', 'Consumíveis')">Consumíveis</a></li>
                                                </ul>
                                            </div>
                                            <div id="edit-erroTipoFornecedor" class="text-danger mt-1 d-none" style="font-size: 0.70rem;">Campo obrigatório.</div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <label class="form-label fw-medium small mb-1">Morada Fiscal / Sede</label>
                                            <input type="text" id="edit-morada" name="edit_morada" class="form-control shadow-sm bg-white" value="">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium small mb-1">Website</label>
                                            <input type="text" id="edit-website" name="edit_website" class="form-control shadow-sm bg-white" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 border-top d-flex justify-content-between mx-n4 mb-n4 bg-light rounded-bottom">
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-brand px-4 btn-wizard-edit-nf" data-bs-wizard-step="#edit-nf-step2-tab">
                                        Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-nf-step2-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-0 pb-4">
                                    <div class="bg-light border rounded-3 p-3 mb-4">
                                        <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2"><i class="fa-solid fa-building me-1 text-brand"></i> Contactos Gerais da Empresa</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small mb-1 text-dark">Telefone Geral *</label>
                                                <input type="tel" id="edit-telefoneGeral" name="edit_telefone_geral" class="form-control shadow-sm bg-white" value="" required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Introduza 9 dígitos numéricos válidos.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small mb-1 text-dark">Email Geral *</label>
                                                <input type="email" id="edit-emailGeral" name="edit_email_geral" class="form-control shadow-sm bg-white" value="" required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Introduza um email válido.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light border rounded-3 p-3">
                                        <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2"><i class="fa-solid fa-user-tie me-1 text-brand"></i> Pessoa de Contacto (Gestor de Conta)</h6>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-medium small mb-1 text-muted">Nome do Responsável</label>
                                                <input type="text" id="edit-nomeResponsavel" name="edit_nome_responsavel" class="form-control shadow-sm bg-white" value="">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium small mb-1 text-muted">Telefone Direto / Telemóvel</label>
                                                <input type="tel" id="edit-telefoneResponsavel" name="edit_telefone_responsavel" class="form-control shadow-sm bg-white" value="">
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Introduza 9 dígitos numéricos válidos.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium small mb-1 text-muted">Email Direto</label>
                                                <input type="email" id="edit-emailResponsavel" name="edit_email_responsavel" class="form-control shadow-sm bg-white" value="">
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">Introduza um email válido.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 border-top d-flex justify-content-between mx-n4 mb-n4 bg-light rounded-bottom">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-wizard-edit-nf" data-bs-wizard-step="#edit-nf-step1-tab">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-brand px-4 btn-wizard-edit-nf" data-bs-wizard-step="#edit-nf-step3-tab">
                                        Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-nf-step3-pane" role="tabpanel" tabindex="0">
                                <div class="card-body p-0 pb-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small mb-1">Notas / Observações</label>
                                        <textarea id="edit-observacoes" name="edit_observacoes" class="form-control shadow-sm bg-white" rows="6"></textarea>
                                    </div>
                                </div>
                                <div class="p-3 border-top d-flex justify-content-between mx-n4 mb-n4 bg-light rounded-bottom">
                                    <button type="button" class="btn btn-outline-secondary px-4 btn-wizard-edit-nf" data-bs-wizard-step="#edit-nf-step2-tab">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                                    </button>
                                    <button type="submit" id="btnGuardarEdicaoFornecedor" form="formEditarFornecedor" class="btn btn-brand px-4 shadow-sm fw-bold">
                                        <i class="fa-solid fa-save me-1"></i> Guardar Alterações
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

<!-- MODAL REMOVER FORNECEDOR -->
<div class="modal fade" id="modalRemoverFornecedor" tabindex="-1" aria-labelledby="modalRemoverFornecedorLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalRemoverFornecedorLabel">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Remover Fornecedor?
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            <div class="modal-body px-4 pt-3 pb-4">
                <!-- Ficha 13: nome preenchido pelo JS -->
                <p class="text-muted mb-0" style="font-size: 1.05rem; line-height: 1.6;">
                    Está prestes a remover o fornecedor
                    <span class="fw-semibold text-dark" id="remover-nomeEmpresa">—</span>.
                </p>
                <!-- Alerta mostrado pelo JS se tiver equipamentos associados -->
                <div id="remover-alertaBloqueio"
                    class="alert alert-danger mt-3 mb-0 border-0 bg-danger bg-opacity-10 text-danger d-flex gap-2 align-items-start d-none"
                    style="font-size: 0.85rem;">
                    <i class="fa-solid fa-circle-info mt-1"></i>
                    <div>
                        <strong>Atenção:</strong> Não é possível remover este fornecedor porque existem
                        <strong id="remover-totalEquipamentos">0</strong> equipamentos associados.
                        Para remover este fornecedor, aceda primeiro à lista de equipamentos,
                        reatribua-os a outro fornecedor ou proceda ao seu abate.
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <!-- Form oculto que submete o DELETE para o PHP -->
                <form id="formRemoverFornecedor" action="lista_fornecedores.php" method="POST">
                    <input type="hidden" name="acao" value="remover_fornecedor">
                    <input type="hidden" name="id_fornecedor" id="remover-idFornecedor" value="">
                </form>
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <!-- Ficha 13: ativado/desativado pelo JS conforme total de equipamentos -->
                <button type="submit" form="formRemoverFornecedor" id="btnConfirmarRemover"
                    class="btn btn-danger px-4 fw-bold shadow-sm" disabled>
                    Remover Fornecedor
                </button>
            </div>

        </div>
    </div>
</div>


<!-- Script do Wizard (Passos) -->
<script>
    // Função auxiliar para o dropdown costumizado
    function selecionarDropdownFornecedor(campo, valor) {
        document.getElementById('input' + campo).value = valor;
        const texto = document.getElementById('text' + campo);
        texto.innerText = valor;
        texto.classList.remove('text-muted');
        texto.classList.add('text-dark');

        // Limpa o erro do botão
        const btn = document.getElementById('input' + campo).nextElementSibling;
        btn.classList.remove('is-invalid', 'border-danger');
        document.getElementById('erroTipoFornecedor').classList.add('d-none');
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Padrões de Validação
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{9}$/;

        // 1. Navegação Passo-a-Passo com Validação
        document.querySelectorAll('.btn-wizard-nf').forEach(button => {
            button.addEventListener('click', function() {
                const targetTabId = this.getAttribute('data-bs-wizard-step');
                const painelAtual = document.querySelector('#modalNovoFornecedor .tab-pane.active');

                if (this.innerText.toLowerCase().includes("anterior")) {
                    mudarAbaFornecedor(targetTabId);
                    return;
                }

                let tudoValido = true;

                // Validar todos os inputs e selects na aba atual
                painelAtual.querySelectorAll('input, select').forEach(campo => {
                    let elementoParaPintar = campo;
                    let campoValido = true;
                    let isRequired = campo.hasAttribute('required');

                    // Tratamento especial para o Dropdown Invisível
                    if (campo.type === 'hidden' && campo.id === 'inputTipoFornecedor') {
                        elementoParaPintar = campo.nextElementSibling;
                        if (isRequired && (!campo.value || campo.value.trim() === '')) {
                            campoValido = false;
                            document.getElementById('erroTipoFornecedor').classList.remove('d-none');
                        } else {
                            document.getElementById('erroTipoFornecedor').classList.add('d-none');
                        }
                    } else {
                        // 1. Validar se está vazio quando é obrigatório
                        if (isRequired && (!campo.value || campo.value.trim() === '')) {
                            campoValido = false;
                        }

                        // 2. Validar URL do Website (NOVO)
                        if (campo.name === 'website' && campo.value.trim() !== '') {
                            const websiteRegex = /^(https?:\/\/)?([\w\-]+(\.[\w\-]+)+)([\/\w\-]*)*\/?$/i;
                            if (!websiteRegex.test(campo.value.trim())) campoValido = false;
                        }

                        // 3. Validar Telefones
                        if (campo.value && campo.type === 'tel') {
                            if (!phoneRegex.test(campo.value.trim())) campoValido = false;
                        }

                        // 4. Validar Emails
                        if (campo.value && campo.type === 'email') {
                            if (!emailRegex.test(campo.value.trim())) campoValido = false;
                        }

                        // 5. Validar NIF
                        if (campo.name === 'nif' && campo.value) {
                            if (!phoneRegex.test(campo.value.trim())) campoValido = false;
                        }

                        // 6. VALIDAÇÃO CONDICIONAL (Pessoa de Contacto)
                        const nomeResp = document.querySelector('#modalNovoFornecedor input[name="nome_responsavel"]').value.trim();
                        if (campo.name === 'telefone_direto' || campo.name === 'email_direto') {
                            // Se tem nome preenchido, estes campos não podem estar vazios
                            if (nomeResp !== '' && campo.value.trim() === '') {
                                campoValido = false;
                            }
                        }
                    }

                    // Aplicar classes de erro
                    if (!campoValido) {
                        elementoParaPintar.classList.add('is-invalid', 'border-danger');
                        tudoValido = false;
                    } else if (elementoParaPintar) {
                        elementoParaPintar.classList.remove('is-invalid', 'border-danger');
                    }
                });

                if (tudoValido) {
                    mudarAbaFornecedor(targetTabId);
                }
            });
        });

        // Função auxiliar para mudar a aba visualmente
        function mudarAbaFornecedor(targetTabId) {
            new bootstrap.Tab(document.querySelector(targetTabId)).show();

            const tabIdDoBotao = targetTabId.replace('-pane', '-tab').replace('#', '');
            document.querySelectorAll('#novoFornecedorTabs .nav-link').forEach(navLink => {
                const badge = navLink.querySelector('.badge');
                if (navLink.id === tabIdDoBotao) {
                    navLink.classList.remove('text-muted');
                    navLink.classList.add('active', 'text-dark');
                    badge.classList.replace('bg-secondary', 'bg-brand');
                } else {
                    navLink.classList.remove('active', 'text-dark');
                    navLink.classList.add('text-muted');
                    badge.classList.replace('bg-brand', 'bg-secondary');
                }
            });
        }

        // 2. Limpar o erro visual quando o utilizador começa a corrigir
        document.querySelectorAll('#formNovoFornecedor input, #formNovoFornecedor select').forEach(campo => {
            campo.addEventListener('input', function() {
                this.classList.remove('is-invalid', 'border-danger');
            });
        });

        // 3. Efeito Final de Submissão
        const formNovoFornecedor = document.getElementById('formNovoFornecedor');
        if (formNovoFornecedor) {
            formNovoFornecedor.addEventListener('submit', function(e) {
                // A linha "e.preventDefault();" foi removida para deixar o PHP receber os dados

                const btnSubmit = document.getElementById('btnCriarFornecedor');
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> A criar...';
                btnSubmit.disabled = true;

                // O setTimeout também foi removido, porque o formulário vai mudar de página agora!
            });
        }
    });
</script>

<script>
    // Função para o dropdown de Editar
    function selecionarDropdownEditFornecedor(campo, valor) {
        document.getElementById('edit-input' + campo).value = valor;
        const texto = document.getElementById('edit-text' + campo);
        texto.innerText = valor;
        texto.classList.remove('text-muted');
        texto.classList.add('text-dark');

        const btn = document.getElementById('edit-input' + campo).nextElementSibling;
        btn.classList.remove('is-invalid', 'border-danger');
        document.getElementById('edit-erro' + campo).classList.add('d-none');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{9}$/;

        // 1. Navegação Passo-a-Passo
        document.querySelectorAll('.btn-wizard-edit-nf').forEach(button => {
            button.addEventListener('click', function() {
                const targetTabId = this.getAttribute('data-bs-wizard-step');
                const painelAtual = document.querySelector('#modalEditarFornecedor .tab-pane.active');

                if (this.innerText.toLowerCase().includes("anterior")) {
                    mudarAbaEditFornecedor(targetTabId);
                    return;
                }

                let tudoValido = true;

                painelAtual.querySelectorAll('input, select').forEach(campo => {
                    let elementoParaPintar = campo;
                    let campoValido = true;
                    let isRequired = campo.hasAttribute('required');

                    if (campo.type === 'hidden' && campo.id === 'edit-inputTipoFornecedor') {
                        elementoParaPintar = campo.nextElementSibling;
                        if (isRequired && (!campo.value || campo.value.trim() === '')) {
                            campoValido = false;
                            document.getElementById('edit-erroTipoFornecedor').classList.remove('d-none');
                        } else {
                            document.getElementById('edit-erroTipoFornecedor').classList.add('d-none');
                        }
                    } else {
                        if (isRequired && (!campo.value || campo.value.trim() === '')) campoValido = false;
                        if (campo.value && campo.type === 'tel' && !phoneRegex.test(campo.value.trim())) campoValido = false;
                        if (campo.value && campo.id === 'edit-nifFornecedor' && !phoneRegex.test(campo.value.trim())) campoValido = false;
                        if (campo.value && campo.type === 'email' && !emailRegex.test(campo.value.trim())) campoValido = false;
                    }

                    if (!campoValido) {
                        elementoParaPintar.classList.add('is-invalid', 'border-danger');
                        tudoValido = false;
                    } else if (elementoParaPintar) {
                        elementoParaPintar.classList.remove('is-invalid', 'border-danger');
                    }
                });

                if (tudoValido) mudarAbaEditFornecedor(targetTabId);
            });
        });

        function mudarAbaEditFornecedor(targetTabId) {
            new bootstrap.Tab(document.querySelector(targetTabId)).show();
            const tabIdDoBotao = targetTabId.replace('-pane', '-tab').replace('#', '');
            document.querySelectorAll('#editarFornecedorTabs .nav-link').forEach(navLink => {
                const badge = navLink.querySelector('.badge');
                if (navLink.id === tabIdDoBotao) {
                    navLink.classList.remove('text-muted');
                    navLink.classList.add('active', 'text-dark');
                    badge.classList.replace('bg-secondary', 'bg-brand');
                } else {
                    navLink.classList.remove('active', 'text-dark');
                    navLink.classList.add('text-muted');
                    badge.classList.replace('bg-brand', 'bg-secondary');
                }
            });
        }

        // 2. Limpar erro visual
        document.querySelectorAll('#formEditarFornecedor input, #formEditarFornecedor select').forEach(campo => {
            campo.addEventListener('input', function() {
                this.classList.remove('is-invalid', 'border-danger');
            });
        });

        // 3. Submissão
        const formEditarFornecedor = document.getElementById('formEditarFornecedor');
        if (formEditarFornecedor) {
            formEditarFornecedor.addEventListener('submit', function() {
                const btnSubmit = document.getElementById('btnGuardarEdicaoFornecedor');
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> A guardar...';
                btnSubmit.disabled = true;
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
const idFornAbrir = params.get('abrir');

if (idFornAbrir) {
    const botaoVer = document.querySelector('.btn-ver-forn[data-id="' + idFornAbrir + '"]');
    if (botaoVer) {
        botaoVer.click();
        // Limpar o parâmetro da URL sem recarregar a página
        window.history.replaceState(null, null, window.location.pathname);
    }
}

        // Mapeamento de tipo de fornecedor para classe CSS do badge
        const badgeClasses = {
            'Fabricante': 'badge-fabricante',
            'Distribuidor': 'badge-distribuidor',
            'Assistência Técnica': 'badge-assistencia',
            'Consumíveis': 'badge-consumiveis'
        };

        // Mapeamento de estado do equipamento para classe CSS do badge
        const estadoClasses = {
    'Ativo':           'st-ativo',
    'Em Manutenção':   'st-manutencao',
    'Em Calibração':   'st-calibracao',
    'Inativo':         'st-inativo',
    'Em Quarentena':   'st-quarentena',
    'Abatido':         'st-abatido'
};

        // ----------------------------------------------------------
        // BOTÕES "VER" NA TABELA
        // Ao clicar, faz fetch ao get_fornecedor.php e preenche
        // o modal com os dados reais da BD (Ficha 13 - Passo 2 e 3)
        // ----------------------------------------------------------
        document.querySelectorAll('.btn-ver-forn').forEach(function(botao) {
            botao.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                // Feedback visual no botão enquanto carrega
                const textoOriginal = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                const botaoAtual = this;

                // Repor o tbody com mensagem de carregamento
                document.getElementById('detalhe-tbodyEquipamentos').innerHTML =
                    '<tr><td colspan="5" class="text-center py-4 text-muted small">' +
                    '<i class="fa-solid fa-spinner fa-spin me-2"></i> A carregar...</td></tr>';

                // Ficha 13 - Passo 1: pedido GET à API com o ID do fornecedor
                fetch('api/get_fornecedor.php?id=' + id)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        botaoAtual.innerHTML = textoOriginal;

                        if (data.sucesso) {
                            const f = data.dados;
                            const equipamentos = data.equipamentos;

                            // ------------------------------------------
                            // Ficha 13 - Passo 3: preencher o modal
                            // ------------------------------------------

                            // Header: nome e badge de tipo
                            document.getElementById('detalhe-nomeEmpresa').textContent = f.nome_empresa;

                            const badgeEl = document.getElementById('detalhe-badgeTipo');
                            const classeBase = badgeClasses[f.tipo_fornecedor] || 'badge-fabricante';
                            badgeEl.className = 'badge-tipo ' + classeBase + ' border';
                            document.getElementById('detalhe-tipoTexto').textContent = f.tipo_fornecedor;

                            // Guardar o ID nos botões de ação do header do modal
                            // para os listeners de Editar e Remover saberem qual fornecedor é
                            document.getElementById('detalhe-btnEditar').setAttribute('data-id', f.id_fornecedor);
                            document.getElementById('detalhe-btnRemover').setAttribute('data-id', f.id_fornecedor);
                            document.getElementById('detalhe-btnRemover').setAttribute('data-equipamentos', equipamentos.length);

                            // Informação Geral
                            document.getElementById('detalhe-nif').textContent = f.nif || 'N/D';
                            document.getElementById('detalhe-telefoneGeral').textContent = f.telefone_geral || '—';
                            document.getElementById('detalhe-emailGeral').textContent = f.email_geral || '—';
                            document.getElementById('detalhe-morada').textContent = f.morada || '—';

                            // Website: só mostra link clicável se existir
                            const websiteEl = document.getElementById('detalhe-websiteLink');
                            if (f.website && f.website.trim() !== '') {
                                websiteEl.textContent = f.website;
                                websiteEl.href = f.website.startsWith('http') ? f.website : 'https://' + f.website;
                            } else {
                                websiteEl.textContent = '—';
                                websiteEl.href = '#';
                            }

                            // Observações
                            document.getElementById('detalhe-observacoes').textContent =
                                (f.observacoes && f.observacoes.trim() !== '') ? f.observacoes : 'Sem observações registadas.';

                            // Responsável: mostra o card só se existir nome
                            const secaoResp = document.getElementById('detalhe-secaoResponsavel');
                            if (f.nome_responsavel && f.nome_responsavel.trim() !== '') {
                                secaoResp.classList.remove('d-none');
                                document.getElementById('detalhe-avatarInicial').textContent = f.nome_responsavel.charAt(0).toUpperCase();
                                document.getElementById('detalhe-nomeResponsavel').textContent = f.nome_responsavel;
                                document.getElementById('detalhe-telefoneResponsavel').textContent = f.telefone_responsavel || '—';
                                document.getElementById('detalhe-emailResponsavel').textContent = f.email_responsavel || '—';
                            } else {
                                secaoResp.classList.add('d-none');
                            }

                            // ------------------------------------------
                            // Tabela de equipamentos associados
                            // O JS constrói as linhas <tr> dinamicamente
                            // ------------------------------------------
                            document.getElementById('detalhe-totalEquipamentos').textContent = equipamentos.filter(function(eq) { return eq.estado !== 'Abatido'; }).length;

                            const tbody = document.getElementById('detalhe-tbodyEquipamentos');

                            if (equipamentos.length === 0) {
    tbody.innerHTML =
        '<tr><td colspan="6" class="text-center py-4 text-muted small">' +
        'Sem equipamentos associados.</td></tr>';
} else {
    // Filtrar os equipamentos Abatidos — já saíram do inventário
    const equipamentosAtivos = equipamentos.filter(function(eq) {
        return eq.estado !== 'Abatido';
    });

    if (equipamentosAtivos.length === 0) {
        tbody.innerHTML =
            '<tr><td colspan="6" class="text-center py-4 text-muted small">' +
            'Sem equipamentos ativos associados.</td></tr>';
    } else {
        tbody.innerHTML = '';
        equipamentosAtivos.forEach(function (eq) {
            const classeEstado = estadoClasses[eq.estado] || 'st-inativo';
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="px-4 py-3 text-brand fw-bold small">' + eq.codigo_interno + '</td>' +
                '<td class="px-3 py-3 fw-medium text-dark small">' + eq.designacao + '</td>' +
                '<td class="px-3 py-3 text-muted small">' + (eq.nome_servico || '—') + '</td>' +
                '<td class="px-3 py-3 text-muted small">' + eq.relacao + '</td>' +
                '<td class="px-3 py-3"><span class="badge-eq ' + classeEstado + '" style="font-size: 0.75rem; padding: 0.3rem 0.5rem;"><span class="dot"></span>' + eq.estado + '</span></td>' +
                '<td class="px-3 py-3 text-end">' +
    '<a href="../../views/equipamentos/lista_equi.php?abrir=' + eq.id_equipamento_enc + '&origem=fornecedor&id_fornecedor=' + f.id_fornecedor + '" ' +
    'class="btn btn-sm btn-brand-subtle text-brand fw-semibold shadow-none" ' +
    'style="font-size: 0.75rem;">Ver ficha →</a>' +
'</td>'
            tbody.appendChild(tr);
        });
    }
}

                            // Abrir o modal
                            new bootstrap.Modal(document.getElementById('modalDetalheFornecedor')).show();

                        } else {
                            alert('Erro ao carregar dados: ' + data.erro);
                        }
                    })
                    .catch(function(err) {
                        botaoAtual.innerHTML = textoOriginal;
                        console.error('Erro AJAX:', err);
                        alert('Ocorreu um erro de comunicação com o servidor.');
                    });
            });
        });

        // Botão Remover direto da tabela
        document.querySelectorAll('.btn-remover-forn').forEach(function(botao) {
            botao.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                fetch('api/get_fornecedor.php?id=' + id)
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        if (data.sucesso) {
                            const f = data.dados;
                            const total = data.equipamentos.length;

                            document.getElementById('remover-idFornecedor').value = f.id_fornecedor;
                            document.getElementById('remover-nomeEmpresa').textContent = '"' + f.nome_empresa + '"';
                            document.getElementById('remover-totalEquipamentos').textContent = total;

                            const alertaBloqueio = document.getElementById('remover-alertaBloqueio');
                            const btnConfirmar = document.getElementById('btnConfirmarRemover');

                            if (total > 0) {
                                alertaBloqueio.classList.remove('d-none');
                                btnConfirmar.disabled = true;
                            } else {
                                alertaBloqueio.classList.add('d-none');
                                btnConfirmar.disabled = false;
                            }

                            new bootstrap.Modal(document.getElementById('modalRemoverFornecedor')).show();
                        }
                    });
            });
        });

        // ----------------------------------------------------------
        // BOTÃO "EDITAR" dentro do modal de detalhes
        // Por agora só fecha o modal de detalhes e abre o de editar.
        // O preenchimento do formulário de edição será implementado
        // na fase seguinte (modal de editar).
        // ----------------------------------------------------------
        document.getElementById('detalhe-btnEditar').addEventListener('click', function() {
            const id = this.getAttribute('data-id');

            // Ficha 13: segundo fetch para garantir dados mais recentes (suporte multi-utilizador)
            fetch('api/get_fornecedor.php?id=' + id)
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    if (data.sucesso) {
                        const f = data.dados;

                        // Preencher o ID oculto para o PHP saber qual fornecedor atualizar
                        document.getElementById('edit-idFornecedor').value = f.id_fornecedor;

                        // Step 1
                        document.getElementById('edit-nomeEmpresa').value = f.nome_empresa;
                        document.getElementById('edit-nifFornecedor').value = f.nif || '';
                        document.getElementById('edit-morada').value = f.morada || '';
                        document.getElementById('edit-website').value = f.website || '';
                        selecionarDropdownEditFornecedor('TipoFornecedor', f.tipo_fornecedor);

                        // Step 2
                        document.getElementById('edit-telefoneGeral').value = f.telefone_geral || '';
                        document.getElementById('edit-emailGeral').value = f.email_geral || '';
                        document.getElementById('edit-nomeResponsavel').value = f.nome_responsavel || '';
                        document.getElementById('edit-telefoneResponsavel').value = f.telefone_responsavel || '';
                        document.getElementById('edit-emailResponsavel').value = f.email_responsavel || '';

                        // Step 3
                        document.getElementById('edit-observacoes').value = f.observacoes || '';

                        // Repor o wizard no Step 1 antes de abrir
                        new bootstrap.Tab(document.getElementById('edit-nf-step1-tab')).show();
                        document.querySelectorAll('#editarFornecedorTabs .nav-link').forEach(function(tab) {
                            tab.classList.add('text-muted');
                            tab.classList.remove('active', 'text-dark');
                            tab.querySelector('.badge').className = 'badge bg-secondary text-white me-1 rounded-pill';
                        });
                        const step1Tab = document.getElementById('edit-nf-step1-tab');
                        step1Tab.classList.remove('text-muted');
                        step1Tab.classList.add('active', 'text-dark');
                        step1Tab.querySelector('.badge').className = 'badge bg-brand text-white me-1 rounded-pill';

                        // Fechar o modal de detalhes e abrir o de editar
                        bootstrap.Modal.getInstance(document.getElementById('modalDetalheFornecedor')).hide();
                        new bootstrap.Modal(document.getElementById('modalEditarFornecedor')).show();
                    } else {
                        alert('Erro ao carregar dados para edição: ' + data.erro);
                    }
                })
                .catch(function(err) {
                    console.error('Erro AJAX:', err);
                    alert('Ocorreu um erro de comunicação.');
                });
        });

        // ----------------------------------------------------------
        // BOTÃO "REMOVER" dentro do modal de detalhes
        // Será implementado na fase seguinte (modal de remover).
        // ----------------------------------------------------------
        document.getElementById('detalhe-btnRemover').addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const totalEquipamentos = parseInt(this.getAttribute('data-equipamentos') || '0');
            const nomeEmpresa = document.getElementById('detalhe-nomeEmpresa').textContent;

            // Preencher o modal de remover com os dados que já temos
            document.getElementById('remover-idFornecedor').value = id;
            document.getElementById('remover-nomeEmpresa').textContent = '"' + nomeEmpresa + '"';
            document.getElementById('remover-totalEquipamentos').textContent = totalEquipamentos;

            const alertaBloqueio = document.getElementById('remover-alertaBloqueio');
            const btnConfirmar = document.getElementById('btnConfirmarRemover');

            if (totalEquipamentos > 0) {
                alertaBloqueio.classList.remove('d-none');
                btnConfirmar.disabled = true;
            } else {
                alertaBloqueio.classList.add('d-none');
                btnConfirmar.disabled = false;
            }

            bootstrap.Modal.getInstance(document.getElementById('modalDetalheFornecedor')).hide();
            new bootstrap.Modal(document.getElementById('modalRemoverFornecedor')).show();
        });

    });
</script>

<?php if (!empty($erros) || !empty($erro_sistema)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modalForm = new bootstrap.Modal(document.getElementById('modalNovoFornecedor'));
            modalForm.show();
            <?php if (!empty($erro_sistema)): ?>
                alert("ERRO NA BASE DE DADOS: <?= $erro_sistema ?>");
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

<?php if ($sucesso == 1): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("✅ Fornecedor registado com sucesso!");
            window.history.replaceState(null, null, window.location.pathname);
        });
    </script>
<?php endif; ?>

<?php if ($sucesso == 2): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("✅ Fornecedor atualizado com sucesso!");
            window.history.replaceState(null, null, window.location.pathname);
        });
    </script>
<?php endif; ?>

<?php if ($sucesso == 3): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("✅ Fornecedor removido com sucesso!");
            window.history.replaceState(null, null, window.location.pathname);
        });
    </script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>