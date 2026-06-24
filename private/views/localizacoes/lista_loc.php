<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$erro_sistema = "";
$sucesso = isset($_GET['sucesso']) ? (int)$_GET['sucesso'] : 0;

// Função auxiliar para criar ligação PDO (evita repetição nos 12 blocos)
function ligacaoBD() {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
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
            // Código 23000 = FK violation (tem pisos associados)
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
            header("Location: lista_loc.php?sucesso=4");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro_sistema = "Já existe um piso com essa designação neste edifício.";
            } else {
                $erro_sistema = "Erro ao criar piso: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=5");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro_sistema = "Já existe um piso com essa designação neste edifício.";
            } else {
                $erro_sistema = "Erro ao editar piso: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=6");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro_sistema = "Não é possível remover este piso porque tem serviços associados.";
            } else {
                $erro_sistema = "Erro ao remover piso: " . $e->getMessage();
            }
        }
    }
}

// -------------------------------------------------------
// SERVIÇOS
// -------------------------------------------------------

// FICHA 13: NOVO SERVIÇO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'novo_servico') {
    $id_piso            = (int)($_POST['id_piso'] ?? 0);
    $nome               = trim($_POST['nome'] ?? '');
    $diretor_responsavel = trim($_POST['diretor_responsavel'] ?? '');
    $centro_custo       = trim($_POST['centro_custo'] ?? '');

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
            header("Location: lista_loc.php?sucesso=7");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao criar serviço: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=8");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao editar serviço: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=9");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro_sistema = "Não é possível remover este serviço porque tem salas ou equipamentos associados.";
            } else {
                $erro_sistema = "Erro ao remover serviço: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=10");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao criar sala: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=11");
            exit;
        } catch (PDOException $e) {
            $erro_sistema = "Erro ao editar sala: " . $e->getMessage();
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
            header("Location: lista_loc.php?sucesso=12");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro_sistema = "Não é possível remover esta sala porque tem equipamentos associados.";
            } else {
                $erro_sistema = "Erro ao remover sala: " . $e->getMessage();
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
            <button class="btn btn-light border-0 shadow-sm"><i class="fa-solid fa-bars"></i></button>
        </header>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-3">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Localizações</h1>
                <p class="text-muted small mb-0" id="header-subtitle">3 edifícios · 1426 equipamentos registados</p>
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

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
                <div class="col">
                    <div class="card dash-card h-100 border-0 shadow-sm card-hover cursor-pointer"
                        onclick="goToPisos('Edifício Principal')">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                                    style="width: 52px; height: 52px; font-size: 1.5rem;">
                                    <i class="fa-regular fa-building"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Edifício Principal</h5>
                                    <span class="text-muted" style="font-size: 0.8rem;">Edifício central do
                                        hospital</span>
                                </div>
                            </div>
                            <div class="row g-2 mb-4">
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 text-center h-100">
                                        <div class="fs-5 fw-bold text-dark">3</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Pisos</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 text-center h-100">
                                        <div class="fs-5 fw-bold text-dark">7</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Serviços</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2 text-center h-100">
                                        <div class="fs-5 fw-bold text-dark">842</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Equips.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-brand small fw-semibold d-flex align-items-center gap-1">Ver pisos <i
                                        class="fa-solid fa-arrow-right"></i></span>

                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                        onclick="event.stopPropagation();" data-bs-toggle="modal"
                                        data-bs-target="#modalEditarEdificio" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                        onclick="event.stopPropagation();" data-bs-toggle="modal"
                                        data-bs-target="#modalRemoverEdificio" title="Remover">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 card-dashed bg-transparent cursor-pointer" data-bs-toggle="modal"
                        data-bs-target="#modalNovoEdificio" style="min-height: 220px;">
                        <div
                            class="card-body d-flex flex-column align-items-center justify-content-center text-muted transition-all">
                            <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mb-3"
                                style="width: 50px; height: 50px;"><i class="fa-solid fa-plus fs-5 text-brand"></i>
                            </div>
                            <span class="fw-semibold text-dark">Adicionar Edifício</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="view-pisos" class="loc-view d-none">
            <p class="text-muted small mb-3"><i class="fa-solid fa-circle-info me-1 text-brand"></i> Pisos do <strong
                    id="lbl-edificio">Edifício Principal</strong>. Clica num piso para ver os serviços.</p>

            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                <div class="col">
                    <div class="card dash-card h-100 border-0 shadow-sm card-hover cursor-pointer"
                        onclick="goToServicos('Edifício Principal', 'Piso 1')">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                                    style="width: 44px; height: 44px; font-size: 1.2rem;">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Piso 1</h6>
                                    <span class="text-muted" style="font-size: 0.75rem;">UCI e Bloco Operatório</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mb-3">
                                <div class="bg-light rounded px-3 py-2 flex-fill text-center">
                                    <div class="fs-5 fw-bold text-dark">2</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Serviços</div>
                                </div>
                                <div class="bg-light rounded px-3 py-2 flex-fill text-center">
                                    <div class="fs-5 fw-bold text-dark">312</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Equips.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-brand small fw-semibold d-flex align-items-center gap-1">Ver Serviços <i
                                        class="fa-solid fa-arrow-right"></i></span>

                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                        onclick="event.stopPropagation();" data-bs-toggle="modal"
                                        data-bs-target="#modalEditarPiso" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                        onclick="event.stopPropagation();" data-bs-toggle="modal"
                                        data-bs-target="#modalRemoverPiso" title="Remover">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 card-dashed bg-transparent cursor-pointer" data-bs-toggle="modal"
                        data-bs-target="#modalNovoPiso">
                        <div
                            class="card-body d-flex flex-column align-items-center justify-content-center text-muted transition-all">
                            <i class="fa-solid fa-plus fs-5 mb-2 text-brand"></i>
                            <span class="fw-semibold text-dark small">Adicionar Piso</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="view-servicos" class="loc-view d-none">
            <p class="text-muted small mb-3"><i class="fa-solid fa-circle-info me-1 text-brand"></i> Serviços do <strong
                    id="lbl-piso">Piso 1</strong>. Clica num serviço para ver as salas.</p>

            <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                <div class="col">
                    <div class="card dash-card h-100 border-0 shadow-sm card-hover cursor-pointer"
                        onclick="goToSalas('Edifício Principal', 'Piso 1', 'UCI')">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-brand-subtle text-brand"
                                        style="width: 44px; height: 44px; font-size: 1.2rem;">
                                        <i class="fa-solid fa-heart-pulse"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">UCI</h6>
                                        <span class="text-muted" style="font-size: 0.75rem;">4 salas</span>
                                    </div>
                                </div>
                                <span class="badge badge-soft-danger"><i
                                        class="fa-solid fa-triangle-exclamation me-1"></i> 48 Críticos</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between text-muted small mb-1"
                                    style="font-size: 0.75rem;">
                                    <span>Estado dos equipamentos</span><span>186 total</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 90%"
                                        aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 10%"
                                        aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-brand small fw-semibold d-flex align-items-center gap-1">Ver Salas <i
                                        class="fa-solid fa-arrow-right"></i></span>

                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1"
                                        onclick="event.stopPropagation();" data-bs-toggle="modal"
                                        data-bs-target="#modalEditarServico" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1"
                                        onclick="event.stopPropagation();" data-bs-toggle="modal"
                                        data-bs-target="#modalRemoverServico" title="Remover">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 card-dashed bg-transparent cursor-pointer" data-bs-toggle="modal"
                        data-bs-target="#modalNovoServico">
                        <div
                            class="card-body d-flex flex-column align-items-center justify-content-center text-muted transition-all">
                            <i class="fa-solid fa-plus fs-5 mb-2 text-brand"></i>
                            <span class="fw-semibold text-dark small">Adicionar Serviço</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="view-salas" class="loc-view d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted small mb-0"><i class="fa-solid fa-circle-info me-1 text-brand"></i> Visão geral do
                    serviço <strong id="lbl-servico">UCI</strong>.</p>
            </div>

            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col"><div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 shadow-none h-100 p-3"><h3 class="fw-bold text-primary mb-0">186</h3><span class="text-muted small">Equipamentos</span></div></div>
                <div class="col"><div class="card border border-success-subtle bg-success-subtle bg-opacity-10 shadow-none h-100 p-3"><h3 class="fw-bold text-success mb-0">174</h3><span class="text-muted small">Ativos</span></div></div>
                <div class="col"><div class="card border border-warning-subtle bg-warning-subtle bg-opacity-10 shadow-none h-100 p-3"><h3 class="fw-bold text-warning mb-0">12</h3><span class="text-muted small">Em Manutenção</span></div></div>
                <div class="col"><div class="card border border-danger-subtle bg-danger-subtle bg-opacity-10 shadow-none h-100 p-3"><h3 class="fw-bold text-danger mb-0">48</h3><span class="text-muted small">Suporte de Vida</span></div></div>
            </div>

            <h6 class="fw-bold text-dark mb-3 mt-4">Salas Registadas</h6>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
                
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 p-3 card-hover">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                    <i class="fa-solid fa-door-closed"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">UCI-1</h6>
                                    <span class="text-muted" style="font-size: 0.75rem;">Sala de Isolamento</span>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditarSala" title="Editar"><i class="fa-solid fa-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalRemoverSala" title="Remover"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                        
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-solid fa-stethoscope text-brand me-1"></i> 12 Equip.</span>
                            <a href="../equipamentos/lista_equi.html" class="btn btn-sm btn-brand-subtle text-brand fw-semibold text-decoration-none px-3 shadow-none">
                                Ver Equipamentos &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card border-0 shadow-sm h-100 p-3 card-hover">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                    <i class="fa-solid fa-door-closed"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">UCI-2</h6>
                                    <span class="text-muted" style="font-size: 0.75rem;">Quarto Standard</span>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light text-secondary border-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditarSala" title="Editar"><i class="fa-solid fa-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-light text-danger border-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalRemoverSala" title="Remover"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-solid fa-stethoscope text-brand me-1"></i> 8 Equip.</span>
                            <a href="../equipamentos/lista_equi.html" class="btn btn-sm btn-brand-subtle text-brand fw-semibold text-decoration-none px-3 shadow-none">
                                Ver Equipamentos &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 card-dashed bg-transparent cursor-pointer" data-bs-toggle="modal" data-bs-target="#modalNovaSala" style="min-height: 120px;">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted p-2">
                            <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mb-2" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-plus text-brand"></i>
                            </div>
                            <span class="fw-semibold text-dark small text-center">Nova Sala</span>
                        </div>
                    </div>
                </div>

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
                    <form id="formNovoEdificio">

                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Nome do Edifício *</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Ex: Edifício Central"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium small mb-1">Descrição</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Ex: Maternidade e Pediatria">
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <p class="fw-semibold text-dark mb-2 small"><i
                                    class="fa-solid fa-layer-group me-1 text-brand"></i> Geração de Pisos</p>
                            <p class="text-muted mb-3" style="font-size: 0.75rem;">Defina a quantidade de pisos. O
                                sistema irá criá-los automaticamente (ex: Piso 0, Piso 1...).</p>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium small mb-1 text-muted">Pisos acima do
                                        solo</label>
                                    <input type="number" class="form-control shadow-sm" placeholder="Ex: 3" min="1"
                                        required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium small mb-1 text-muted">Pisos subterrâneos</label>
                                    <input type="number" class="form-control shadow-sm" placeholder="Ex: 1" min="0"
                                        value="0">
                                </div>
                            </div>
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
                    <form id="formNovoPiso">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-medium small mb-1">Piso (Nº/Nome) *</label>
                                <input type="text" class="form-control shadow-sm" placeholder="Ex: Piso 2" required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-medium small mb-1">Serviço</label>
                                <input type="text" class="form-control shadow-sm" placeholder="Ex: Consultas Externas">
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
                    <form id="formNovoServico">
                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Nome do Serviço *</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Ex: Cardiologia" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Diretor / Responsável (Opcional)</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Nome do responsável">
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium small mb-1">Centro de Custo (Opcional)</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Ex: CC-12345">
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
                    <form id="formNovaSala">

                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Nome / Identificação da Sala *</label>
                            <input type="text" class="form-control shadow-sm"
                                placeholder="Ex: Gabinete 3, BO-1, Enfermaria A..." required>
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-medium small mb-1">Observações (Opcional)</label>
                            <textarea class="form-control shadow-sm" rows="2"
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
                    <form id="formEditarEdificio">
                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Nome do Edifício *</label>
                            <input type="text" class="form-control shadow-sm" value="Edifício Principal" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium small mb-1">Descrição</label>
                            <input type="text" class="form-control shadow-sm" value="Edifício central do hospital">
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
                    <form id="formEditarPiso">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-medium small mb-1">Piso (Nº/Nome) *</label>
                                <input type="text" class="form-control shadow-sm" value="Piso 1" required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-medium small mb-1">Descrição</label>
                                <input type="text" class="form-control shadow-sm" value="UCI e Bloco Operatório">
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
                    <form id="formEditarServico">
                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Nome do Serviço *</label>
                            <input type="text" class="form-control shadow-sm" value="UCI" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Diretor / Responsável (Opcional)</label>
                            <input type="text" class="form-control shadow-sm" value="Dr. João Silva">
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium small mb-1">Centro de Custo (Opcional)</label>
                            <input type="text" class="form-control shadow-sm" value="CC-98765">
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
                    <form id="formEditarSala">
                        <div class="mb-3">
                            <label class="form-label fw-medium small mb-1">Nome / Identificação da Sala *</label>
                            <input type="text" class="form-control shadow-sm" value="UCI-1" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium small mb-1">Observações (Opcional)</label>
                            <textarea class="form-control shadow-sm"
                                rows="2">Sala com isolamento de pressão negativa.</textarea>
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
                    <p class="text-muted mb-0" style="font-size: 1.05rem;">Está prestes a remover o <span
                            class="fw-semibold text-dark">Edifício Principal</span>.</p>
                    <div
                        class="alert alert-danger mt-3 mb-0 border-0 bg-danger bg-opacity-10 text-danger d-flex gap-2 align-items-start small">
                        <i class="fa-solid fa-circle-info mt-1"></i>
                        <div><strong>Aviso Crítico:</strong> Esta ação apagará também todos os pisos, serviços e salas
                            associados a este edifício. Só é possível se não existirem equipamentos alocados.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm">Remover Edifício</button>
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
                    <p class="text-muted mb-0" style="font-size: 1.05rem;">Está prestes a remover o <span
                            class="fw-semibold text-dark">Piso 1</span> do Edifício Principal.</p>
                    <div
                        class="alert alert-warning mt-3 mb-0 border-0 bg-warning bg-opacity-10 text-dark d-flex gap-2 align-items-start small">
                        <i class="fa-solid fa-circle-info mt-1"></i>
                        <div><strong>Aviso:</strong> Isto removerá também os serviços associados a este piso.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm">Remover Piso</button>
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
                    <p class="text-muted mb-0" style="font-size: 1.05rem;">Está prestes a remover o serviço <span
                            class="fw-semibold text-dark">UCI</span>.</p>
                    <p class="text-muted small mt-2 mb-0">Certifique-se de que não existem salas ativas com equipamentos
                        pendentes de transferência.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm">Remover Serviço</button>
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
                    <p class="text-muted mb-0" style="font-size: 1.05rem;">Vai remover a sala <span
                            class="fw-semibold text-dark">UCI-1</span>.</p>
                    <div
                        class="alert alert-danger mt-3 mb-0 border-0 bg-danger bg-opacity-10 text-danger d-flex gap-2 align-items-start small">
                        <i class="fa-solid fa-circle-info mt-1"></i>
                        <div>Não é possível remover a sala enquanto existirem equipamentos associados a ela (Atualmente:
                            2 equipamentos).</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm" disabled>Remover Sala</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Apanhar todos os formulários que estão dentro de modais
            const formsModais = document.querySelectorAll('.modal form');

            formsModais.forEach(form => {
                // Truque Mágico: Adicionar o novalidate via Javascript
                form.setAttribute('novalidate', true);

                // 2. Quando o utilizador clica em "Criar" ou "Guardar"
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); 

                    let tudoValido = true;

                    // A) Validar se os campos obrigatórios (required) estão preenchidos
                    const camposObrigatorios = form.querySelectorAll('input[required], select[required]');
                    camposObrigatorios.forEach(campo => {
                        if (!campo.value || campo.value.trim() === '') {
                            campo.classList.add('is-invalid', 'border-danger');
                            tudoValido = false;
                        } else {
                            campo.classList.remove('is-invalid', 'border-danger');
                        }
                    });

                    // B) Validar regras numéricas
                    const camposNumericos = form.querySelectorAll('input[type="number"]');
                    camposNumericos.forEach(campo => {
                        if (campo.value && campo.hasAttribute('min')) {
                            const minVal = parseFloat(campo.getAttribute('min'));
                            if (parseFloat(campo.value) < minVal) {
                                campo.classList.add('is-invalid', 'border-danger');
                                tudoValido = false;
                            }
                        }
                    });

                    if (!tudoValido) return; 

                    // C) EFEITO VISUAL DE SUBMISSÃO E FECHO DO MODAL
                    const formId = form.getAttribute('id');
                    const btnSubmit = document.querySelector(`button[type="submit"][form="${formId}"]`);
                    
                    if (btnSubmit) {
                        const originalText = btnSubmit.innerHTML;
                        
                        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> A guardar...';
                        btnSubmit.disabled = true;

                        setTimeout(() => {
                            alert("✅ Registo guardado com sucesso!");
                            
                            // 1. Encontrar o modal que contém este formulário
                            const modalElement = form.closest('.modal');
                            
                            // 2. Fechar o modal usando a API do Bootstrap
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            
                            // 3. Voltar a pôr o botão ao normal (para quando o modal for aberto de novo)
                            btnSubmit.innerHTML = originalText;
                            btnSubmit.disabled = false;
                            
                            // 4. Limpar o formulário se for um modal de "Novo" (evita limpar nos de "Editar")
                            if(formId.includes("Novo")) {
                                form.reset();
                            }

                        }, 1000);
                    }
                });

                // UX: Limpar a borda vermelha ao escrever
                form.querySelectorAll('input, select').forEach(campo => {
                    campo.addEventListener('input', function() {
                        this.classList.remove('is-invalid', 'border-danger');
                    });
                });
            });
        });
    </script>

    <script>
        function hideAllViews() {
            document.querySelectorAll('.loc-view').forEach(function (view) {
                view.classList.add('d-none');
            });
        }

        // Navegar para Nível 1: Edifícios
        function goToEdificios() {
            hideAllViews();
            document.getElementById('view-edificios').classList.remove('d-none');

            // Atualizar Breadcrumb
            document.getElementById('dynamic-breadcrumb').innerHTML = `
                <li class="breadcrumb-item active fw-bold text-brand" aria-current="page">
                    <i class="fa-solid fa-sitemap me-1"></i> Edifícios
                </li>
            `;
        }

        // Navegar para Nível 2: Pisos
        function goToPisos(edificioNome) {
            hideAllViews();
            document.getElementById('view-pisos').classList.remove('d-none');
            document.getElementById('lbl-edificio').innerText = edificioNome;

            // Atualizar Breadcrumb
            document.getElementById('dynamic-breadcrumb').innerHTML = `
                <li class="breadcrumb-item"><a href="#" onclick="goToEdificios(); return false;" class="text-decoration-none text-muted">Edifícios</a></li>
                <li class="breadcrumb-item active fw-bold text-brand" aria-current="page">${edificioNome}</li>
            `;
        }

        // Navegar para Nível 3: Serviços
        function goToServicos(edificioNome, pisoNome) {
            hideAllViews();
            document.getElementById('view-servicos').classList.remove('d-none');
            document.getElementById('lbl-piso').innerText = pisoNome;

            // Atualizar Breadcrumb
            document.getElementById('dynamic-breadcrumb').innerHTML = `
                <li class="breadcrumb-item"><a href="#" onclick="goToEdificios(); return false;" class="text-decoration-none text-muted">Edifícios</a></li>
                <li class="breadcrumb-item"><a href="#" onclick="goToPisos('${edificioNome}'); return false;" class="text-decoration-none text-muted">${edificioNome}</a></li>
                <li class="breadcrumb-item active fw-bold text-brand" aria-current="page">${pisoNome}</li>
            `;
        }

        // Navegar para Nível 4: Salas/Equipamentos
        function goToSalas(edificioNome, pisoNome, servicoNome) {
            hideAllViews();
            document.getElementById('view-salas').classList.remove('d-none');
            document.getElementById('lbl-servico').innerText = servicoNome;

            // Atualizar Breadcrumb
            document.getElementById('dynamic-breadcrumb').innerHTML = `
                <li class="breadcrumb-item"><a href="#" onclick="goToEdificios(); return false;" class="text-decoration-none text-muted">Edifícios</a></li>
                <li class="breadcrumb-item"><a href="#" onclick="goToPisos('${edificioNome}'); return false;" class="text-decoration-none text-muted">${edificioNome}</a></li>
                <li class="breadcrumb-item"><a href="#" onclick="goToServicos('${edificioNome}', '${pisoNome}'); return false;" class="text-decoration-none text-muted">${pisoNome}</a></li>
                <li class="breadcrumb-item active fw-bold text-brand" aria-current="page">${servicoNome}</li>
            `;
        }
    </script>

<?php include '../../includes/footer.php'; ?>