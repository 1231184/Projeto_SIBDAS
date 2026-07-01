<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$erro_sistema = "";
$sucesso = isset($_GET['sucesso']) ? (int)$_GET['sucesso'] : 0;

// Função auxiliar para criar ligação PDO (evita repetição nos 12 blocos)
function ligacaoBD()
{
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

// -------------------------------------------------------
// EDIFÍCIOS
// -------------------------------------------------------

// FICHA 13: NOVO EDIFÍCIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'novo_edificio') {
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if (empty($nome)) {
        $erro_sistema = "O nome do edifício é obrigatório.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("INSERT INTO edificios (nome, descricao) VALUES (:nome, :descricao)");
            $stmt->execute([
                ':nome'      => $nome,
                ':descricao' => !empty($descricao) ? $descricao : null
            ]);
            header("Location: lista_loc.php?sucesso=1");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao criar edifício: " . $e->getMessage();
        }
    }
}

// FICHA 13: EDITAR EDIFÍCIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'editar_edificio') {
    $id        = (int)($_POST['id_edificio'] ?? 0);
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if (empty($nome) || $id <= 0) {
        $erro_sistema = "Dados inválidos para editar edifício.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("UPDATE edificios SET nome = :nome, descricao = :descricao WHERE id_edificio = :id");
            $stmt->execute([
                ':nome'      => $nome,
                ':descricao' => !empty($descricao) ? $descricao : null,
                ':id'        => $id
            ]);
            header("Location: lista_loc.php?sucesso=2");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao editar edifício: " . $e->getMessage();
        }
    }
}

// FICHA 13: REMOVER EDIFÍCIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'remover_edificio') {
    $id = (int)($_POST['id_edificio'] ?? 0);

    if ($id <= 0) {
        $erro_sistema = "ID de edifício inválido.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("DELETE FROM edificios WHERE id_edificio = :id");
            $stmt->execute([':id' => $id]);
            header("Location: lista_loc.php?sucesso=3");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro_sistema = "Não é possível remover este edifício porque tem pisos associados.";
            } else {
                $erro_sistema = "Erro ao remover edifício: " . $e->getMessage();
            }
        }
    }
}

// -------------------------------------------------------
// PISOS
// -------------------------------------------------------

// FICHA 13: NOVO PISO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'novo_piso') {
    $id_edificio = (int)($_POST['id_edificio'] ?? 0);
    $designacao  = trim($_POST['designacao'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if (empty($designacao) || $id_edificio <= 0) {
        $erro_sistema = "A designação do piso é obrigatória.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("INSERT INTO pisos (id_edificio, designacao, observacoes) VALUES (:id_edificio, :designacao, :observacoes)");
            $stmt->execute([
                ':id_edificio' => $id_edificio,
                ':designacao'  => $designacao,
                ':observacoes' => !empty($observacoes) ? $observacoes : null
            ]);
            header("Location: lista_loc.php?sucesso=4&nivel=pisos&id=" . $id_edificio);
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header("Location: lista_loc.php?erro=piso_duplicado&nivel=pisos&id=" . $id_edificio);
                exit;
            } else {
                header("Location: lista_loc.php?erro=sistema&nivel=pisos&id=" . $id_edificio);
                exit;
            }
        }
    }
}

// FICHA 13: EDITAR PISO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'editar_piso') {
    $id          = (int)($_POST['id_piso'] ?? 0);
    $designacao  = trim($_POST['designacao'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if (empty($designacao) || $id <= 0) {
        $erro_sistema = "Dados inválidos para editar piso.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("UPDATE pisos SET designacao = :designacao, observacoes = :observacoes WHERE id_piso = :id");
            $stmt->execute([
                ':designacao'  => $designacao,
                ':observacoes' => !empty($observacoes) ? $observacoes : null,
                ':id'          => $id
            ]);
            $row = $pdo->prepare("SELECT id_edificio FROM pisos WHERE id_piso = :id");
            $row->execute([':id' => $id]);
            $id_edificio_ret = $row->fetchColumn();
            header("Location: lista_loc.php?sucesso=5&nivel=pisos&id=" . $id_edificio_ret);
            exit;
        } catch (PDOException $e) {
            // Neste caso o SELECT ainda não correu, por isso lemos o id_edificio do POST
            $id_edificio_ret = (int)($_POST['id_edificio_ret'] ?? 0);
            if ($e->getCode() == 23000) {
                header("Location: lista_loc.php?erro=piso_duplicado&nivel=pisos&id=" . $id_edificio_ret);
                exit;
            } else {
                header("Location: lista_loc.php?erro=sistema&nivel=pisos&id=" . $id_edificio_ret);
                exit;
            }
        }
    }
}

// FICHA 13: REMOVER PISO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'remover_piso') {
    $id = (int)($_POST['id_piso'] ?? 0);

    if ($id <= 0) {
        $erro_sistema = "ID de piso inválido.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("DELETE FROM pisos WHERE id_piso = :id");
            $stmt->execute([':id' => $id]);
            $id_edificio_ret = (int)($_POST['id_edificio'] ?? 0);
            header("Location: lista_loc.php?sucesso=6&nivel=pisos&id=" . $id_edificio_ret);
            exit;
        } catch (PDOException $e) {
            $id_edificio_ret = (int)($_POST['id_edificio'] ?? 0);
            if ($e->getCode() == 23000) {
                header("Location: lista_loc.php?erro=piso_tem_servicos&nivel=pisos&id=" . $id_edificio_ret);
                exit;
            } else {
                header("Location: lista_loc.php?erro=sistema&nivel=pisos&id=" . $id_edificio_ret);
                exit;
            }
        }
    }
}

// -------------------------------------------------------
// SERVIÇOS
// -------------------------------------------------------

// FICHA 13: NOVO SERVIÇO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'novo_servico') {
    $id_piso             = (int)($_POST['id_piso'] ?? 0);
    $nome                = trim($_POST['nome'] ?? '');
    $diretor_responsavel = trim($_POST['diretor_responsavel'] ?? '');
    $centro_custo        = trim($_POST['centro_custo'] ?? '');

    if (empty($nome) || $id_piso <= 0) {
        $erro_sistema = "O nome do serviço é obrigatório.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("INSERT INTO servicos (id_piso, nome, diretor_responsavel, centro_custo) VALUES (:id_piso, :nome, :diretor, :custo)");
            $stmt->execute([
                ':id_piso' => $id_piso,
                ':nome'    => $nome,
                ':diretor' => !empty($diretor_responsavel) ? $diretor_responsavel : null,
                ':custo'   => !empty($centro_custo) ? $centro_custo : null
            ]);
            header("Location: lista_loc.php?sucesso=7&nivel=servicos&id=" . $id_piso);
            exit;
        } catch (PDOException $e) {
            header("Location: lista_loc.php?erro=sistema&nivel=servicos&id=" . $id_piso);
            exit;
        }
    }
}

// FICHA 13: EDITAR SERVIÇO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'editar_servico') {
    $id                  = (int)($_POST['id_servico'] ?? 0);
    $nome                = trim($_POST['nome'] ?? '');
    $diretor_responsavel = trim($_POST['diretor_responsavel'] ?? '');
    $centro_custo        = trim($_POST['centro_custo'] ?? '');

    if (empty($nome) || $id <= 0) {
        $erro_sistema = "Dados inválidos para editar serviço.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("UPDATE servicos SET nome = :nome, diretor_responsavel = :diretor, centro_custo = :custo WHERE id_servico = :id");
            $stmt->execute([
                ':nome'    => $nome,
                ':diretor' => !empty($diretor_responsavel) ? $diretor_responsavel : null,
                ':custo'   => !empty($centro_custo) ? $centro_custo : null,
                ':id'      => $id
            ]);
            $row = $pdo->prepare("SELECT id_piso FROM servicos WHERE id_servico = :id");
            $row->execute([':id' => $id]);
            $id_piso_ret = $row->fetchColumn();
            header("Location: lista_loc.php?sucesso=8&nivel=servicos&id=" . $id_piso_ret);
            exit;
        } catch (PDOException $e) {
            $id_piso_ret = (int)($_POST['id_piso_ret'] ?? 0);
            header("Location: lista_loc.php?erro=sistema&nivel=servicos&id=" . $id_piso_ret);
            exit;
        }
    }
}

// FICHA 13: REMOVER SERVIÇO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'remover_servico') {
    $id = (int)($_POST['id_servico'] ?? 0);

    if ($id <= 0) {
        $erro_sistema = "ID de serviço inválido.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("DELETE FROM servicos WHERE id_servico = :id");
            $stmt->execute([':id' => $id]);
            $id_piso_ret = (int)($_POST['id_piso'] ?? 0);
            header("Location: lista_loc.php?sucesso=9&nivel=servicos&id=" . $id_piso_ret);
            exit;
        } catch (PDOException $e) {
            $id_piso_ret = (int)($_POST['id_piso'] ?? 0);
            if ($e->getCode() == 23000) {
                header("Location: lista_loc.php?erro=servico_tem_salas&nivel=servicos&id=" . $id_piso_ret);
                exit;
            } else {
                header("Location: lista_loc.php?erro=sistema&nivel=servicos&id=" . $id_piso_ret);
                exit;
            }
        }
    }
}

// -------------------------------------------------------
// SALAS
// -------------------------------------------------------

// FICHA 13: NOVA SALA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'nova_sala') {
    $id_servico    = (int)($_POST['id_servico'] ?? 0);
    $identificacao = trim($_POST['identificacao'] ?? '');
    $observacoes   = trim($_POST['observacoes'] ?? '');

    if (empty($identificacao) || $id_servico <= 0) {
        $erro_sistema = "A identificação da sala é obrigatória.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("INSERT INTO salas (id_servico, identificacao, observacoes) VALUES (:id_servico, :identificacao, :observacoes)");
            $stmt->execute([
                ':id_servico'    => $id_servico,
                ':identificacao' => $identificacao,
                ':observacoes'   => !empty($observacoes) ? $observacoes : null
            ]);
            header("Location: lista_loc.php?sucesso=10&nivel=salas&id=" . $id_servico);
            exit;
        } catch (PDOException $e) {
            header("Location: lista_loc.php?erro=sistema&nivel=salas&id=" . $id_servico);
            exit;
        }
    }
}

// FICHA 13: EDITAR SALA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'editar_sala') {
    $id            = (int)($_POST['id_sala'] ?? 0);
    $identificacao = trim($_POST['identificacao'] ?? '');
    $observacoes   = trim($_POST['observacoes'] ?? '');

    if (empty($identificacao) || $id <= 0) {
        $erro_sistema = "Dados inválidos para editar sala.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("UPDATE salas SET identificacao = :identificacao, observacoes = :observacoes WHERE id_sala = :id");
            $stmt->execute([
                ':identificacao' => $identificacao,
                ':observacoes'   => !empty($observacoes) ? $observacoes : null,
                ':id'            => $id
            ]);
            $row = $pdo->prepare("SELECT id_servico FROM salas WHERE id_sala = :id");
            $row->execute([':id' => $id]);
            $id_servico_ret = $row->fetchColumn();
            header("Location: lista_loc.php?sucesso=11&nivel=salas&id=" . $id_servico_ret);
            exit;
        } catch (PDOException $e) {
            $id_servico_ret = (int)($_POST['id_servico_ret'] ?? 0);
            header("Location: lista_loc.php?erro=sistema&nivel=salas&id=" . $id_servico_ret);
            exit;
        }
    }
}

// FICHA 13: REMOVER SALA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'remover_sala') {
    $id = (int)($_POST['id_sala'] ?? 0);

    if ($id <= 0) {
        $erro_sistema = "ID de sala inválido.";
    } else {
        try {
            $pdo = ligacaoBD();
            $stmt = $pdo->prepare("DELETE FROM salas WHERE id_sala = :id");
            $stmt->execute([':id' => $id]);
            $id_servico_ret = (int)($_POST['id_servico'] ?? 0);
            header("Location: lista_loc.php?sucesso=12&nivel=salas&id=" . $id_servico_ret);
            exit;
        } catch (PDOException $e) {
            $id_servico_ret = (int)($_POST['id_servico'] ?? 0);
            if ($e->getCode() == 23000) {
                header("Location: lista_loc.php?erro=sala_tem_equip&nivel=salas&id=" . $id_servico_ret);
                exit;
            } else {
                header("Location: lista_loc.php?erro=sistema&nivel=salas&id=" . $id_servico_ret);
                exit;
            }
        }
    }
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
        <button class="btn btn-light border-0 shadow-sm d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"><i class="fa-solid fa-bars"></i></button>
    </header>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Localizações</h1>
            <p class="text-muted small mb-0" id="header-subtitle">A carregar...</p>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="mb-4 pb-3 border-bottom">
        <ol class="breadcrumb mb-0" id="dynamic-breadcrumb">
            <li class="breadcrumb-item active fw-bold text-brand" aria-current="page">
                <i class="fa-solid fa-sitemap me-1"></i> Edifícios
            </li>
        </ol>
    </nav>

    <div id="view-edificios" class="loc-view">
        <div id="grid-edificios" class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
            <!-- preenchido pelo JS -->
        </div>
    </div>

    <div id="view-pisos" class="loc-view d-none">
        <p class="text-muted small mb-3"><i class="fa-solid fa-circle-info me-1 text-brand"></i> Pisos do <strong
                id="lbl-edificio">Edifício Principal</strong>. Clica num piso para ver os serviços.</p>

        <div id="grid-pisos" class="row row-cols-1 row-cols-md-3 g-4 mb-4">
            <!-- preenchido pelo JS -->
        </div>
    </div>

    <div id="view-servicos" class="loc-view d-none">
        <p class="text-muted small mb-3"><i class="fa-solid fa-circle-info me-1 text-brand"></i> Serviços do <strong
                id="lbl-piso">Piso 1</strong>. Clica num serviço para ver as salas.</p>

        <div id="grid-servicos" class="row row-cols-1 row-cols-md-2 g-4 mb-4">
            <!-- preenchido pelo JS -->
        </div>
    </div>

    <div id="view-salas" class="loc-view d-none">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted small mb-0"><i class="fa-solid fa-circle-info me-1 text-brand"></i> Visão geral do
                serviço <strong id="lbl-servico">UCI</strong>.</p>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
            <div class="col">
                <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 shadow-none h-100 p-3">
                    <h3 class="fw-bold text-primary mb-0" id="stat-total">0</h3><span class="text-muted small">Equipamentos</span>
                </div>
            </div>
            <div class="col">
                <div class="card border border-success-subtle bg-success-subtle bg-opacity-10 shadow-none h-100 p-3">
                    <h3 class="fw-bold text-success mb-0" id="stat-ativos">0</h3><span class="text-muted small">Ativos</span>
                </div>
            </div>
            <div class="col">
                <div class="card border border-warning-subtle bg-warning-subtle bg-opacity-10 shadow-none h-100 p-3">
                    <h3 class="fw-bold text-warning mb-0" id="stat-manutencao">0</h3><span class="text-muted small">Em Manutenção</span>
                </div>
            </div>
            <div class="col">
                <div class="card border border-danger-subtle bg-danger-subtle bg-opacity-10 shadow-none h-100 p-3">
                    <h3 class="fw-bold text-danger mb-0" id="stat-criticos">0</h3><span class="text-muted small">Suporte de Vida</span>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3 mt-4">Salas Registadas</h6>

        <div id="grid-salas" class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
            <!-- preenchido pelo JS -->
        </div>
    </div>

</main>

<!-- Modal Novo Edificio, Piso, Serviço e Sala -->
<div class="modal fade" id="modalNovoEdificio" tabindex="-1" aria-labelledby="modalNovoEdificioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNovoEdificioLabel">Novo Edifício
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Adicione um novo edifício ao complexo</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formNovoEdificio" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="novo_edificio">

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Nome do Edifício *</label>
                        <input type="text" id="novo-edificio-nome" name="nome"
                            class="form-control shadow-sm" placeholder="Ex: Edifício Central" required>
                        <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Descrição</label>
                        <input type="text" id="novo-edificio-descricao" name="descricao"
                            class="form-control shadow-sm" placeholder="Ex: Maternidade e Pediatria">
                    </div>

                </form>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNovoEdificio" class="btn btn-brand px-4 shadow-sm fw-bold">
                    Criar Edifício
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalNovoPiso" tabindex="-1" aria-labelledby="modalNovoPisoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNovoPisoLabel">Novo Piso</h5>
                        <p class="text-muted small mb-0 mt-1">Adicionar ao <span id="modal-lbl-edificio-piso"
                                class="fw-medium text-dark">Edifício Atual</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formNovoPiso" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="novo_piso">
                    <input type="hidden" name="id_edificio" id="novo-piso-id-edificio" value="">

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-medium small mb-1">Designação *</label>
                            <input type="text" id="novo-piso-designacao" name="designacao"
                                class="form-control shadow-sm" placeholder="Ex: Piso 2" required>
                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-medium small mb-1">Observações</label>
                            <input type="text" id="novo-piso-observacoes" name="observacoes"
                                class="form-control shadow-sm" placeholder="Ex: Área técnica restrita">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNovoPiso" class="btn btn-brand px-4 shadow-sm fw-bold">Criar
                    Piso</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNovoServico" tabindex="-1" aria-labelledby="modalNovoServicoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNovoServicoLabel">Novo Serviço</h5>
                        <p class="text-muted small mb-0 mt-1">Adicionar ao <span class="fw-medium text-dark">Piso
                                Atual</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formNovoServico" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="novo_servico">
                    <input type="hidden" name="id_piso" id="novo-servico-id-piso" value="">

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Nome do Serviço *</label>
                        <input type="text" id="novo-servico-nome" name="nome"
                            class="form-control shadow-sm" placeholder="Ex: Cardiologia" required>
                        <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Diretor / Responsável (Opcional)</label>
                        <input type="text" id="novo-servico-diretor" name="diretor_responsavel"
                            class="form-control shadow-sm" placeholder="Nome do responsável">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-medium small mb-1">Centro de Custo (Opcional)</label>
                        <input type="text" id="novo-servico-custo" name="centro_custo"
                            class="form-control shadow-sm" placeholder="Ex: CC-12345">
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNovoServico" class="btn btn-brand px-4 shadow-sm fw-bold">Criar
                    Serviço</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNovaSala" tabindex="-1" aria-labelledby="modalNovaSalaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-door-closed"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNovaSalaLabel">Nova Sala</h5>
                        <p class="text-muted small mb-0 mt-1">Adicionar ao serviço atual</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formNovaSala" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="nova_sala">
                    <input type="hidden" name="id_servico" id="nova-sala-id-servico" value="">

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Nome / Identificação da Sala *</label>
                        <input type="text" id="nova-sala-identificacao" name="identificacao"
                            class="form-control shadow-sm" placeholder="Ex: Gabinete 3, BO-1, Enfermaria A..." required>
                        <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-medium small mb-1">Observações (Opcional)</label>
                        <textarea id="nova-sala-observacoes" name="observacoes"
                            class="form-control shadow-sm" rows="2"
                            placeholder="Notas adicionais sobre este espaço..."></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNovaSala" class="btn btn-brand px-4 shadow-sm fw-bold">Criar
                    Sala</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Editar Edificio, Piso, Serviço e Sala -->
<div class="modal fade" id="modalEditarEdificio" tabindex="-1" aria-labelledby="modalEditarEdificioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarEdificioLabel">Editar Edifício
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Atualize os dados do edifício selecionado</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formEditarEdificio" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="editar_edificio">
                    <input type="hidden" name="id_edificio" id="editar-edificio-id" value="">

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Nome do Edifício *</label>
                        <input type="text" id="editar-edificio-nome" name="nome"
                            class="form-control shadow-sm" value="" required>
                        <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-medium small mb-1">Descrição</label>
                        <input type="text" id="editar-edificio-descricao" name="descricao"
                            class="form-control shadow-sm" value="">
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarEdificio" class="btn btn-brand px-4 shadow-sm fw-bold">Guardar
                    Alterações</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarPiso" tabindex="-1" aria-labelledby="modalEditarPisoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarPisoLabel">Editar Piso</h5>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formEditarPiso" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="editar_piso">
                    <input type="hidden" name="id_piso" id="editar-piso-id" value="">

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-medium small mb-1">Designação *</label>
                            <input type="text" id="editar-piso-designacao" name="designacao"
                                class="form-control shadow-sm" value="" required>
                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-medium small mb-1">Observações</label>
                            <input type="text" id="editar-piso-observacoes" name="observacoes"
                                class="form-control shadow-sm" value="">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarPiso" class="btn btn-brand px-4 shadow-sm fw-bold">Guardar
                    Alterações</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarServico" tabindex="-1" aria-labelledby="modalEditarServicoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarServicoLabel">Editar Serviço
                        </h5>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formEditarServico" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="editar_servico">
                    <input type="hidden" name="id_servico" id="editar-servico-id" value="">

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Nome do Serviço *</label>
                        <input type="text" id="editar-servico-nome" name="nome"
                            class="form-control shadow-sm" value="" required>
                        <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Diretor / Responsável (Opcional)</label>
                        <input type="text" id="editar-servico-diretor" name="diretor_responsavel"
                            class="form-control shadow-sm" value="">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-medium small mb-1">Centro de Custo (Opcional)</label>
                        <input type="text" id="editar-servico-custo" name="centro_custo"
                            class="form-control shadow-sm" value="">
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarServico" class="btn btn-brand px-4 shadow-sm fw-bold">Guardar
                    Alterações</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarSala" tabindex="-1" aria-labelledby="modalEditarSalaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                        style="width: 42px; height: 42px; font-size: 1.25rem;">
                        <i class="fa-solid fa-door-closed"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarSalaLabel">Editar Sala</h5>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formEditarSala" action="lista_loc.php" method="POST" novalidate>
                    <input type="hidden" name="acao" value="editar_sala">
                    <input type="hidden" name="id_sala" id="editar-sala-id" value="">

                    <div class="mb-3">
                        <label class="form-label fw-medium small mb-1">Nome / Identificação da Sala *</label>
                        <input type="text" id="editar-sala-identificacao" name="identificacao"
                            class="form-control shadow-sm" value="" required>
                        <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-medium small mb-1">Observações (Opcional)</label>
                        <textarea id="editar-sala-observacoes" name="observacoes"
                            class="form-control shadow-sm" rows="2"></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarSala" class="btn btn-brand px-4 shadow-sm fw-bold">Guardar
                    Alterações</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Remover Edificio, Piso, Serviço e Sala -->
<div class="modal fade" id="modalRemoverEdificio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Remover Edifício?</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <p class="text-muted mb-0" style="font-size: 1.05rem;">Está prestes a remover o
                    <span class="fw-semibold text-dark" id="remover-edificio-nome">—</span>.
                </p>
                <div class="alert alert-danger mt-3 mb-0 border-0 bg-danger bg-opacity-10 text-danger d-flex gap-2 align-items-start small">
                    <i class="fa-solid fa-circle-info mt-1"></i>
                    <div><strong>Aviso Crítico:</strong> Só é possível remover se não existirem pisos associados.</div>
                </div>
                <form id="formRemoverEdificio" action="lista_loc.php" method="POST" class="d-none">
                    <input type="hidden" name="acao" value="remover_edificio">
                    <input type="hidden" name="id_edificio" id="remover-edificio-id" value="">
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formRemoverEdificio" id="btn-remover-edificio"
                    class="btn btn-danger px-4 fw-bold shadow-sm">Remover Edifício</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRemoverPiso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Remover Piso?</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <p class="text-muted mb-0" style="font-size: 1.05rem;">Está prestes a remover o
                    <span class="fw-semibold text-dark" id="remover-piso-nome">—</span>.
                </p>
                <div class="alert alert-warning mt-3 mb-0 border-0 bg-warning bg-opacity-10 text-dark d-flex gap-2 align-items-start small">
                    <i class="fa-solid fa-circle-info mt-1"></i>
                    <div><strong>Aviso:</strong> Só é possível remover se não existirem serviços associados a este piso.</div>
                </div>
                <form id="formRemoverPiso" action="lista_loc.php" method="POST" class="d-none">
                    <input type="hidden" name="acao" value="remover_piso">
                    <input type="hidden" name="id_piso" id="remover-piso-id" value="">
                    <input type="hidden" name="id_edificio" id="remover-piso-id-edificio" value="">
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formRemoverPiso" id="btn-remover-piso"
                    class="btn btn-danger px-4 fw-bold shadow-sm">Remover Piso</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRemoverServico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Remover Serviço?</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <p class="text-muted mb-0" style="font-size: 1.05rem;">Está prestes a remover o serviço
                    <span class="fw-semibold text-dark" id="remover-servico-nome">—</span>.
                </p>
                <p class="text-muted small mt-2 mb-0">Só é possível remover se não existirem salas associadas a este serviço.</p>
                <form id="formRemoverServico" action="lista_loc.php" method="POST" class="d-none">
                    <input type="hidden" name="acao" value="remover_servico">
                    <input type="hidden" name="id_servico" id="remover-servico-id" value="">
                    <input type="hidden" name="id_piso" id="remover-servico-id-piso" value="">
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formRemoverServico" id="btn-remover-servico"
                    class="btn btn-danger px-4 fw-bold shadow-sm">Remover Serviço</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRemoverSala" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Remover Sala?</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <p class="text-muted mb-0" style="font-size: 1.05rem;">Vai remover a sala
                    <span class="fw-semibold text-dark" id="remover-sala-nome">—</span>.
                </p>
                <div id="remover-sala-alerta"
                    class="alert alert-danger mt-3 mb-0 border-0 bg-danger bg-opacity-10 text-danger d-flex gap-2 align-items-start small d-none">
                    <i class="fa-solid fa-circle-info mt-1"></i>
                    <div>Não é possível remover esta sala enquanto existirem equipamentos associados
                        (<strong id="remover-sala-total-equip">0</strong> equipamentos).</div>
                </div>
                <form id="formRemoverSala" action="lista_loc.php" method="POST" class="d-none">
                    <input type="hidden" name="acao" value="remover_sala">
                    <input type="hidden" name="id_sala" id="remover-sala-id" value="">
                    <input type="hidden" name="id_servico" id="remover-sala-id-servico" value="">
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formRemoverSala" id="btn-remover-sala"
                    class="btn btn-danger px-4 fw-bold shadow-sm">Remover Sala</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ============================================================
    // Estado global da navegação — guarda o contexto atual
    // para que os modais de criar/editar saibam a que pai pertencem
    // ============================================================
    const estado = {
        id_edificio: null,
        nome_edificio: null,
        id_piso: null,
        nome_piso: null,
        id_servico: null,
        nome_servico: null
    };

    // ============================================================
    // FUNÇÕES DE NAVEGAÇÃO
    // ============================================================

    function hideAllViews() {
        document.querySelectorAll('.loc-view').forEach(v => v.classList.add('d-none'));
    }

    function atualizarSubtitulo(texto) {
        document.getElementById('header-subtitle').textContent = texto;
    }

    // Nível 1 — Edifícios
    function goToEdificios() {
        hideAllViews();
        document.getElementById('view-edificios').classList.remove('d-none');
        document.getElementById('dynamic-breadcrumb').innerHTML =
            '<li class="breadcrumb-item active fw-bold text-brand" aria-current="page">' +
            '<i class="fa-solid fa-sitemap me-1"></i> Edifícios</li>';
        carregarEdificios();
    }

    // Nível 2 — Pisos de um edifício
    function goToPisos(id_edificio, nome_edificio) {
        estado.id_edificio = id_edificio;
        estado.nome_edificio = nome_edificio;

        hideAllViews();
        document.getElementById('view-pisos').classList.remove('d-none');
        document.getElementById('lbl-edificio').textContent = nome_edificio;

        document.getElementById('dynamic-breadcrumb').innerHTML =
            '<li class="breadcrumb-item"><a href="#" onclick="goToEdificios(); return false;" ' +
            'class="text-decoration-none text-muted">Edifícios</a></li>' +
            '<li class="breadcrumb-item active fw-bold text-brand" aria-current="page">' + nome_edificio + '</li>';

        carregarPisos(id_edificio);
    }

    // Nível 3 — Serviços de um piso
    function goToServicos(id_piso, nome_piso) {
        estado.id_piso = id_piso;
        estado.nome_piso = nome_piso;

        hideAllViews();
        document.getElementById('view-servicos').classList.remove('d-none');
        document.getElementById('lbl-piso').textContent = nome_piso;

        document.getElementById('dynamic-breadcrumb').innerHTML =
            '<li class="breadcrumb-item"><a href="#" onclick="goToEdificios(); return false;" ' +
            'class="text-decoration-none text-muted">Edifícios</a></li>' +
            '<li class="breadcrumb-item"><a href="#" onclick="goToPisos(' + estado.id_edificio + ', \'' +
            estado.nome_edificio + '\'); return false;" class="text-decoration-none text-muted">' +
            estado.nome_edificio + '</a></li>' +
            '<li class="breadcrumb-item active fw-bold text-brand" aria-current="page">' + nome_piso + '</li>';

        carregarServicos(id_piso);
    }

    // Nível 4 — Salas de um serviço
    function goToSalas(id_servico, nome_servico) {
        estado.id_servico = id_servico;
        estado.nome_servico = nome_servico;

        hideAllViews();
        document.getElementById('view-salas').classList.remove('d-none');
        document.getElementById('lbl-servico').textContent = nome_servico;

        document.getElementById('dynamic-breadcrumb').innerHTML =
            '<li class="breadcrumb-item"><a href="#" onclick="goToEdificios(); return false;" ' +
            'class="text-decoration-none text-muted">Edifícios</a></li>' +
            '<li class="breadcrumb-item"><a href="#" onclick="goToPisos(' + estado.id_edificio + ', \'' +
            estado.nome_edificio + '\'); return false;" class="text-decoration-none text-muted">' +
            estado.nome_edificio + '</a></li>' +
            '<li class="breadcrumb-item"><a href="#" onclick="goToServicos(' + estado.id_piso + ', \'' +
            estado.nome_piso + '\'); return false;" class="text-decoration-none text-muted">' +
            estado.nome_piso + '</a></li>' +
            '<li class="breadcrumb-item active fw-bold text-brand" aria-current="page">' + nome_servico + '</li>';

        carregarSalas(id_servico);
    }

    // ============================================================
    // FUNÇÕES DE CARREGAMENTO (FETCH → BD → renderizar cards)
    // ============================================================

    function carregarEdificios() {
        const grid = document.getElementById('grid-edificios');
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted small">' +
            '<i class="fa-solid fa-spinner fa-spin me-2"></i> A carregar...</div>';

        fetch('api/get_edificios.php')
            .then(r => r.json())
            .then(data => {
                if (!data.sucesso) {
                    grid.innerHTML = '<div class="col-12 text-danger small">' + data.erro + '</div>';
                    return;
                }

                atualizarSubtitulo(data.dados.length + ' edifício' + (data.dados.length !== 1 ? 's' : '') + ' registado' + (data.dados.length !== 1 ? 's' : ''));
                grid.innerHTML = '';

                data.dados.forEach(e => {
                    grid.innerHTML += `
                <div class="col">
                    <div class="card dash-card h-100 border-0 shadow-sm card-hover cursor-pointer"
                         onclick="goToPisos(${e.id_edificio}, '${e.nome.replace(/'/g, "\\'")}')">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                                     style="width: 52px; height: 52px; font-size: 1.5rem;">
                                    <i class="fa-regular fa-building"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">${e.nome}</h5>
                                    <span class="text-muted" style="font-size: 0.8rem;">${e.descricao || '—'}</span>
                                </div>
                            </div>
                            <div class="row g-2 mb-4">
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 text-center h-100">
                                        <div class="fs-5 fw-bold text-dark">${e.total_pisos}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Pisos</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 text-center h-100">
                                        <div class="fs-5 fw-bold text-dark">${e.total_servicos}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Serviços</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 text-center h-100">
                                        <div class="fs-5 fw-bold text-dark">${e.total_equipamentos}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Equips.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-brand small fw-semibold d-flex align-items-center gap-1">
                                    Ver pisos <i class="fa-solid fa-arrow-right"></i>
                                </span>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                        onclick="event.stopPropagation(); abrirEditarEdificio(${e.id_edificio}, '${e.nome.replace(/'/g, "\\'")}', '${(e.descricao || '').replace(/'/g, "\\'")}')">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                        onclick="event.stopPropagation(); abrirRemoverEdificio(${e.id_edificio}, '${e.nome.replace(/'/g, "\\'")}', ${e.total_pisos})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                });

                // Card "Adicionar Edifício"
                grid.innerHTML += `
            <div class="col">
                <div class="card h-100 card-dashed bg-transparent cursor-pointer"
                     data-bs-toggle="modal" data-bs-target="#modalNovoEdificio" style="min-height: 220px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mb-3"
                             style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-plus fs-5 text-brand"></i>
                        </div>
                        <span class="fw-semibold text-dark">Adicionar Edifício</span>
                    </div>
                </div>
            </div>`;
            })
            .catch(() => {
                grid.innerHTML = '<div class="col-12 text-danger small">Erro de comunicação com o servidor.</div>';
            });
    }

    function carregarPisos(id_edificio) {
        const grid = document.getElementById('grid-pisos');
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted small">' +
            '<i class="fa-solid fa-spinner fa-spin me-2"></i> A carregar...</div>';

        fetch('api/get_pisos.php?id_edificio=' + id_edificio)
            .then(r => r.json())
            .then(data => {
                if (!data.sucesso) {
                    grid.innerHTML = '<div class="col-12 text-danger small">' + data.erro + '</div>';
                    return;
                }

                grid.innerHTML = '';

                data.dados.forEach(p => {
                    grid.innerHTML += `
                <div class="col">
                    <div class="card dash-card h-100 border-0 shadow-sm card-hover cursor-pointer"
                         onclick="goToServicos(${p.id_piso}, '${p.designacao.replace(/'/g, "\\'")}')">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                                     style="width: 44px; height: 44px; font-size: 1.2rem;">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">${p.designacao}</h6>
                                    <span class="text-muted" style="font-size: 0.75rem;">${p.observacoes || '—'}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mb-3">
                                <div class="bg-light rounded px-3 py-2 flex-fill text-center">
                                    <div class="fs-5 fw-bold text-dark">${p.total_servicos}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Serviços</div>
                                </div>
                                <div class="bg-light rounded px-3 py-2 flex-fill text-center">
                                    <div class="fs-5 fw-bold text-dark">${p.total_equipamentos}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Equips.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-brand small fw-semibold d-flex align-items-center gap-1">
                                    Ver Serviços <i class="fa-solid fa-arrow-right"></i>
                                </span>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                        onclick="event.stopPropagation(); abrirEditarPiso(${p.id_piso}, '${p.designacao.replace(/'/g, "\\'")}', '${(p.observacoes || '').replace(/'/g, "\\'")}')">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                        onclick="event.stopPropagation(); abrirRemoverPiso(${p.id_piso}, '${p.designacao.replace(/'/g, "\\'")}', ${p.total_servicos})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                });

                // Card "Adicionar Piso"
                grid.innerHTML += `
            <div class="col">
                <div class="card h-100 card-dashed bg-transparent cursor-pointer"
                     onclick="abrirNovoPiso()" style="min-height: 180px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="fa-solid fa-plus fs-5 mb-2 text-brand"></i>
                        <span class="fw-semibold text-dark small">Adicionar Piso</span>
                    </div>
                </div>
            </div>`;
            })
            .catch(() => {
                grid.innerHTML = '<div class="col-12 text-danger small">Erro de comunicação com o servidor.</div>';
            });
    }

    function carregarServicos(id_piso) {
        const grid = document.getElementById('grid-servicos');
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted small">' +
            '<i class="fa-solid fa-spinner fa-spin me-2"></i> A carregar...</div>';

        fetch('api/get_servicos.php?id_piso=' + id_piso)
            .then(r => r.json())
            .then(data => {
                if (!data.sucesso) {
                    grid.innerHTML = '<div class="col-12 text-danger small">' + data.erro + '</div>';
                    return;
                }

                grid.innerHTML = '';

                data.dados.forEach(s => {
                    const pctAtivos = s.total_equipamentos > 0 ?
                        Math.round((s.total_ativos / s.total_equipamentos) * 100) : 0;
                    const pctManut = s.total_equipamentos > 0 ?
                        Math.round((s.total_manutencao / s.total_equipamentos) * 100) : 0;

                    grid.innerHTML += `
                <div class="col">
                    <div class="card dash-card h-100 border-0 shadow-sm card-hover cursor-pointer"
                         onclick="goToSalas(${s.id_servico}, '${s.nome.replace(/'/g, "\\'")}')">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                                         style="width: 44px; height: 44px; font-size: 1.2rem;">
                                        <i class="fa-solid fa-heart-pulse"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">${s.nome}</h6>
                                        <span class="text-muted" style="font-size: 0.75rem;">${s.total_salas} sala${s.total_salas !== 1 ? 's' : ''}</span>
                                    </div>
                                </div>
                                ${s.total_criticos > 0
                                    ? '<span class="badge badge-soft-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>' + s.total_criticos + ' Críticos</span>'
                                    : ''}
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between text-muted small mb-1" style="font-size: 0.75rem;">
                                    <span>Estado dos equipamentos</span><span>${s.total_equipamentos} total</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: ${pctAtivos}%"></div>
                                    <div class="progress-bar bg-warning" style="width: ${pctManut}%"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-brand small fw-semibold d-flex align-items-center gap-1">
                                    Ver Salas <i class="fa-solid fa-arrow-right"></i>
                                </span>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                        onclick="event.stopPropagation(); abrirEditarServico(${s.id_servico}, '${s.nome.replace(/'/g, "\\'")}', '${(s.diretor_responsavel || '').replace(/'/g, "\\'")}', '${(s.centro_custo || '').replace(/'/g, "\\'")}')">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                        onclick="event.stopPropagation(); abrirRemoverServico(${s.id_servico}, '${s.nome.replace(/'/g, "\\'")}', ${s.total_salas})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                });

                // Card "Adicionar Serviço"
                grid.innerHTML += `
            <div class="col">
                <div class="card h-100 card-dashed bg-transparent cursor-pointer"
                     onclick="abrirNovoServico()" style="min-height: 180px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="fa-solid fa-plus fs-5 mb-2 text-brand"></i>
                        <span class="fw-semibold text-dark small">Adicionar Serviço</span>
                    </div>
                </div>
            </div>`;
            })
            .catch(() => {
                grid.innerHTML = '<div class="col-12 text-danger small">Erro de comunicação com o servidor.</div>';
            });
    }

    function carregarSalas(id_servico) {
        const grid = document.getElementById('grid-salas');
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted small">' +
            '<i class="fa-solid fa-spinner fa-spin me-2"></i> A carregar...</div>';

        fetch('api/get_salas.php?id_servico=' + id_servico)
            .then(r => r.json())
            .then(data => {
                if (!data.sucesso) {
                    grid.innerHTML = '<div class="col-12 text-danger small">' + data.erro + '</div>';
                    return;
                }

                // Preencher os 4 cards de estatísticas do serviço
                const sv = data.servico;
                document.getElementById('stat-total').textContent = sv.total_equipamentos;
                document.getElementById('stat-ativos').textContent = sv.total_ativos;
                document.getElementById('stat-manutencao').textContent = sv.total_manutencao;
                document.getElementById('stat-criticos').textContent = sv.total_criticos;

                grid.innerHTML = '';

                data.salas.forEach(sl => {
                    grid.innerHTML += `
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 p-3 card-hover">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-light text-secondary"
                                     style="width: 44px; height: 44px; font-size: 1.25rem;">
                                    <i class="fa-solid fa-door-closed"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">${sl.identificacao}</h6>
                                    <span class="text-muted" style="font-size: 0.75rem;">${sl.observacoes || '—'}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                    onclick="abrirEditarSala(${sl.id_sala}, '${sl.identificacao.replace(/'/g, "\\'")}', '${(sl.observacoes || '').replace(/'/g, "\\'")}')">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                    onclick="abrirRemoverSala(${sl.id_sala}, '${sl.identificacao.replace(/'/g, "\\'")}', ${sl.total_equipamentos})">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="fa-solid fa-stethoscope text-brand me-1"></i> ${sl.total_equipamentos} Equip.
                            </span>
                            <a href="../equipamentos/lista_equi.php?id_servico=${estado.id_servico}&id_sala=${sl.id_sala}" 
   class="btn btn-sm btn-brand-subtle text-brand fw-semibold text-decoration-none px-3 shadow-none">
    Ver Equipamentos &rarr;
</a>
                        </div>
                    </div>
                </div>`;
                });

                // Card "Nova Sala"
                grid.innerHTML += `
            <div class="col">
                <div class="card h-100 card-dashed bg-transparent cursor-pointer"
                     onclick="abrirNovaSala()" style="min-height: 120px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted p-2">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mb-2"
                             style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-plus text-brand"></i>
                        </div>
                        <span class="fw-semibold text-dark small text-center">Nova Sala</span>
                    </div>
                </div>
            </div>`;
            })
            .catch(() => {
                grid.innerHTML = '<div class="col-12 text-danger small">Erro de comunicação com o servidor.</div>';
            });
    }

    // ============================================================
    // FUNÇÕES PARA ABRIR MODAIS (preencher contexto antes de abrir)
    // ============================================================

    // --- CRIAR ---
    function abrirNovoPiso() {
        document.getElementById('novo-piso-id-edificio').value = estado.id_edificio;
        document.getElementById('modal-lbl-edificio-piso').textContent = estado.nome_edificio;
        document.getElementById('formNovoPiso').reset();
        new bootstrap.Modal(document.getElementById('modalNovoPiso')).show();
    }

    function abrirNovoServico() {
        document.getElementById('novo-servico-id-piso').value = estado.id_piso;
        document.getElementById('formNovoServico').reset();
        new bootstrap.Modal(document.getElementById('modalNovoServico')).show();
    }

    function abrirNovaSala() {
        document.getElementById('nova-sala-id-servico').value = estado.id_servico;
        document.getElementById('formNovaSala').reset();
        new bootstrap.Modal(document.getElementById('modalNovaSala')).show();
    }

    // --- EDITAR ---
    function abrirEditarEdificio(id, nome, descricao) {
        document.getElementById('editar-edificio-id').value = id;
        document.getElementById('editar-edificio-nome').value = nome;
        document.getElementById('editar-edificio-descricao').value = descricao;
        new bootstrap.Modal(document.getElementById('modalEditarEdificio')).show();
    }

    function abrirEditarPiso(id, designacao, observacoes) {
        document.getElementById('editar-piso-id').value = id;
        document.getElementById('editar-piso-designacao').value = designacao;
        document.getElementById('editar-piso-observacoes').value = observacoes;
        new bootstrap.Modal(document.getElementById('modalEditarPiso')).show();
    }

    function abrirEditarServico(id, nome, diretor, custo) {
        document.getElementById('editar-servico-id').value = id;
        document.getElementById('editar-servico-nome').value = nome;
        document.getElementById('editar-servico-diretor').value = diretor;
        document.getElementById('editar-servico-custo').value = custo;
        new bootstrap.Modal(document.getElementById('modalEditarServico')).show();
    }

    function abrirEditarSala(id, identificacao, observacoes) {
        document.getElementById('editar-sala-id').value = id;
        document.getElementById('editar-sala-identificacao').value = identificacao;
        document.getElementById('editar-sala-observacoes').value = observacoes;
        new bootstrap.Modal(document.getElementById('modalEditarSala')).show();
    }

    // --- REMOVER ---
    function abrirRemoverEdificio(id, nome, totalPisos) {
        document.getElementById('remover-edificio-id').value = id;
        document.getElementById('remover-edificio-nome').textContent = nome;
        // Bloquear se tiver pisos
        document.getElementById('btn-remover-edificio').disabled = totalPisos > 0;
        new bootstrap.Modal(document.getElementById('modalRemoverEdificio')).show();
    }

    function abrirRemoverPiso(id, nome, totalServicos) {
        document.getElementById('remover-piso-id').value = id;
        document.getElementById('remover-piso-nome').textContent = nome;
        document.getElementById('btn-remover-piso').disabled = totalServicos > 0;
        document.getElementById('remover-piso-id-edificio').value = estado.id_edificio;
        new bootstrap.Modal(document.getElementById('modalRemoverPiso')).show();
    }

    function abrirRemoverServico(id, nome, totalSalas) {
        document.getElementById('remover-servico-id').value = id;
        document.getElementById('remover-servico-nome').textContent = nome;
        document.getElementById('btn-remover-servico').disabled = totalSalas > 0;
        document.getElementById('remover-servico-id-piso').value = estado.id_piso;
        new bootstrap.Modal(document.getElementById('modalRemoverServico')).show();
    }

    function abrirRemoverSala(id, identificacao, totalEquip) {
        document.getElementById('remover-sala-id').value = id;
        document.getElementById('remover-sala-nome').textContent = identificacao;
        document.getElementById('remover-sala-total-equip').textContent = totalEquip;
        document.getElementById('remover-sala-id-servico').value = estado.id_servico;

        const alerta = document.getElementById('remover-sala-alerta');
        const btn = document.getElementById('btn-remover-sala');
        if (totalEquip > 0) {
            alerta.classList.remove('d-none');
            btn.disabled = true;
        } else {
            alerta.classList.add('d-none');
            btn.disabled = false;
        }
        new bootstrap.Modal(document.getElementById('modalRemoverSala')).show();
    }

    // ============================================================
    // VALIDAÇÃO DOS FORMULÁRIOS DOS MODAIS
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal form').forEach(form => {
            form.setAttribute('novalidate', true);

            form.addEventListener('submit', function(e) {
                // Formulários de remover não têm validação de campos
                if (form.id.startsWith('formRemover')) return;

                let tudoValido = true;
form.querySelectorAll('input[required]').forEach(campo => {
    if (!campo.value || campo.value.trim() === '') {
        campo.classList.add('is-invalid', 'border-danger');
        // Garantir que o feedback correto aparece
        const feedback = campo.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = 'Campo obrigatório.';
        }
        tudoValido = false;
    } else if (campo.id === 'novo-piso-designacao' || campo.id === 'editar-piso-designacao') {
        // Designação do piso tem de conter pelo menos um número
        if (!/\d/.test(campo.value)) {
            campo.classList.add('is-invalid', 'border-danger');
            const feedback = campo.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = 'A designação tem de conter pelo menos um número (ex: Piso 1).';
            }
            tudoValido = false;
        } else {
            campo.classList.remove('is-invalid', 'border-danger');
        }
    } else {
        campo.classList.remove('is-invalid', 'border-danger');
    }
});

                if (!tudoValido) {
                    e.preventDefault();
                    return;
                }

                // Feedback visual no botão de submit
                const btnSubmit = form.closest('.modal').querySelector('button[type="submit"]');
                if (btnSubmit) {
                    btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> A guardar...';
                    btnSubmit.disabled = true;
                }
            });

            form.querySelectorAll('input').forEach(campo => {
                campo.addEventListener('input', function() {
                    this.classList.remove('is-invalid', 'border-danger');
                });
            });
        });

        // Verificar se há nível de retorno na URL após uma operação
        const params = new URLSearchParams(window.location.search);
        const nivel = params.get('nivel');
        const idRetorno = parseInt(params.get('id') || '0');

        if (nivel === 'pisos' && idRetorno) {
            fetch('api/get_edificios.php')
                .then(r => r.json())
                .then(data => {
                    const ed = data.dados.find(e => e.id_edificio == idRetorno);
                    if (ed) {
                        goToPisos(ed.id_edificio, ed.nome);
                        window.history.replaceState(null, null, window.location.pathname);
                    } else carregarEdificios();
                })
                .catch(() => carregarEdificios());

        } else if (nivel === 'servicos' && idRetorno) {
            // idRetorno é o id_piso — precisamos de saber o edifício pai
            fetch('api/get_edificios.php')
                .then(r => r.json())
                .then(data => {
                    const promises = data.dados.map(ed =>
                        fetch('api/get_pisos.php?id_edificio=' + ed.id_edificio)
                        .then(r => r.json())
                        .then(pdata => ({
                            ed,
                            piso: pdata.dados.find(p => p.id_piso == idRetorno)
                        }))
                    );
                    Promise.all(promises).then(resultados => {
                        const match = resultados.find(r => r.piso);
                        if (match) {
                            estado.id_edificio = match.ed.id_edificio;
                            estado.nome_edificio = match.ed.nome;
                            goToServicos(match.piso.id_piso, match.piso.designacao);
                            window.history.replaceState(null, null, window.location.pathname);
                        } else {
                            carregarEdificios();
                        }
                    });
                })
                .catch(() => carregarEdificios());

        } else if (nivel === 'salas' && idRetorno) {
            // idRetorno é o id_servico — precisamos de reconstruir todo o contexto
            fetch('api/get_edificios.php')
                .then(r => r.json())
                .then(data => {
                    const pisosPromises = data.dados.map(ed =>
                        fetch('api/get_pisos.php?id_edificio=' + ed.id_edificio)
                        .then(r => r.json())
                        .then(pdata => ({
                            ed,
                            pisos: pdata.dados
                        }))
                    );
                    Promise.all(pisosPromises).then(edificiosComPisos => {
                        const servicosPromises = [];
                        edificiosComPisos.forEach(({
                            ed,
                            pisos
                        }) => {
                            pisos.forEach(p => {
                                servicosPromises.push(
                                    fetch('api/get_servicos.php?id_piso=' + p.id_piso)
                                    .then(r => r.json())
                                    .then(sdata => ({
                                        ed,
                                        piso: p,
                                        servico: sdata.dados.find(s => s.id_servico == idRetorno)
                                    }))
                                );
                            });
                        });
                        Promise.all(servicosPromises).then(resultados => {
                            const match = resultados.find(r => r.servico);
                            if (match) {
                                estado.id_edificio = match.ed.id_edificio;
                                estado.nome_edificio = match.ed.nome;
                                estado.id_piso = match.piso.id_piso;
                                estado.nome_piso = match.piso.designacao;
                                goToSalas(match.servico.id_servico, match.servico.nome);
                                window.history.replaceState(null, null, window.location.pathname);
                            } else {
                                carregarEdificios();
                            }
                        });
                    });
                })
                .catch(() => carregarEdificios());

        } else {
            carregarEdificios();
        }
    });
</script>

<?php
$erro_codigo = $_GET['erro'] ?? '';
$nivel_ret   = $_GET['nivel'] ?? '';
$id_ret      = (int)($_GET['id'] ?? 0);
?>

<?php if ($sucesso > 0 || !empty($erro_codigo)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const msgs = {
                sucesso: {
                    1: {
                        texto: 'Edifício criado com sucesso!',
                        tipo: 'success'
                    },
                    2: {
                        texto: 'Edifício atualizado com sucesso!',
                        tipo: 'success'
                    },
                    3: {
                        texto: 'Edifício removido com sucesso!',
                        tipo: 'success'
                    },
                    4: {
                        texto: 'Piso criado com sucesso!',
                        tipo: 'success'
                    },
                    5: {
                        texto: 'Piso atualizado com sucesso!',
                        tipo: 'success'
                    },
                    6: {
                        texto: 'Piso removido com sucesso!',
                        tipo: 'success'
                    },
                    7: {
                        texto: 'Serviço criado com sucesso!',
                        tipo: 'success'
                    },
                    8: {
                        texto: 'Serviço atualizado com sucesso!',
                        tipo: 'success'
                    },
                    9: {
                        texto: 'Serviço removido com sucesso!',
                        tipo: 'success'
                    },
                    10: {
                        texto: 'Sala criada com sucesso!',
                        tipo: 'success'
                    },
                    11: {
                        texto: 'Sala atualizada com sucesso!',
                        tipo: 'success'
                    },
                    12: {
                        texto: 'Sala removida com sucesso!',
                        tipo: 'success'
                    }
                },
                erro: {
                    'piso_duplicado': {
                        texto: 'Já existe um piso com essa designação neste edifício.',
                        tipo: 'danger'
                    },
                    'piso_tem_servicos': {
                        texto: 'Não é possível remover este piso porque tem serviços associados.',
                        tipo: 'danger'
                    },
                    'servico_tem_salas': {
                        texto: 'Não é possível remover este serviço porque tem salas ou equipamentos associados.',
                        tipo: 'danger'
                    },
                    'sala_tem_equip': {
                        texto: 'Não é possível remover esta sala porque tem equipamentos associados.',
                        tipo: 'danger'
                    },
                    'sistema': {
                        texto: 'Ocorreu um erro inesperado. Tente novamente.',
                        tipo: 'danger'
                    }
                }
            };

            let entrada = null;
            <?php if ($sucesso > 0): ?>
                entrada = msgs.sucesso[<?= $sucesso ?>];
            <?php elseif (!empty($erro_codigo)): ?>
                entrada = msgs.erro['<?= addslashes($erro_codigo) ?>'];
            <?php endif; ?>

            if (entrada) {
                const toast = document.getElementById('toastFeedback');
                const toastMsg = document.getElementById('toastFeedbackMsg');
                toastMsg.textContent = entrada.texto;
                toast.className = 'toast align-items-center border-0 shadow-lg text-bg-' + entrada.tipo;
                const bsToast = new bootstrap.Toast(toast, {
                    delay: 4000
                });
                bsToast.show();
            }

            window.history.replaceState(null, null, window.location.pathname);
        });
    </script>
<?php endif; ?>

<!-- Toast de feedback -->
<div class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 1090;">
    <div id="toastFeedback" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-medium" id="toastFeedbackMsg"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>