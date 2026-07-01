<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$erros = [];
$erro_sistema = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo_interno       = $_POST["internalCode"]       ?? "";
    $designacao           = $_POST["name"]               ?? "";
    $marca                = $_POST["brand"]              ?? "";
    $modelo               = $_POST["model"]              ?? "";
    $numero_serie         = $_POST["serialNumber"]       ?? "";
    $fabricante           = $_POST["manufacturer"]       ?? "";
    $ano_fabrico          = $_POST["manufacturingYear"]  ?? "";
    $categoria            = $_POST["categoria"]          ?? "";
    $criticidade          = $_POST["criticidade"]        ?? "";
    $data_aquisicao       = $_POST["acquisitionDate"]    ?? "";
    $custo                = $_POST["cost"]               ?? "";
    $tipo_entrada         = $_POST["entryType"]          ?? "";
    $estado               = $_POST["status"]             ?? "";
    $observacoes          = $_POST["observations"]       ?? "";

    $falta_declaracao_ce      = isset($_POST["faltaCE"])      ? 1 : 0;
    $falta_manual_utilizador  = isset($_POST["faltaManual"])  ? 1 : 0;
    $falta_fatura_guia        = isset($_POST["faltaFatura"])  ? 1 : 0;

    $designacao   = trim($designacao);
    $marca        = trim($marca);
    $modelo       = trim($modelo);
    $numero_serie = trim($numero_serie);
    $fabricante   = trim($fabricante);
    $categoria    = trim($categoria);
    $criticidade  = trim($criticidade);
    $tipo_entrada = trim($tipo_entrada);
    $estado       = trim($estado);
    $observacoes  = trim($observacoes);

    if (empty($designacao))   $erros["name"]         = "Campo obrigatório.";
    if (empty($marca))        $erros["brand"]        = "Campo obrigatório.";
    if (empty($modelo))       $erros["model"]        = "Campo obrigatório.";
    if (empty($numero_serie)) $erros["serialNumber"] = "Campo obrigatório.";
    if (empty($fabricante))   $erros["manufacturer"] = "Campo obrigatório.";
    if (empty($categoria))    $erros["categoria"]    = "Campo obrigatório.";
    if (empty($criticidade))  $erros["criticidade"]  = "Campo obrigatório.";
    if (empty($tipo_entrada)) $erros["entryType"]    = "Campo obrigatório.";
    if (empty($estado))       $erros["status"]       = "Campo obrigatório.";
    if (empty($data_aquisicao)) $erros["acquisitionDate"] = "Campo obrigatório.";
    if (empty($ano_fabrico))    $erros["manufacturingYear"] = "Campo obrigatório.";

    if (!empty($designacao) && strlen($designacao) < 3) {
        $erros["name"] = "A designação deve ter pelo menos 3 caracteres.";
    }

    if (!empty($marca) && preg_match('/\d/', $marca)) {
        $erros["brand"] = "A marca não pode conter números.";
    }

    if (!empty($numero_serie) && !preg_match('/^[a-zA-Z0-9\-_.]+$/', $numero_serie)) {
        $erros["serialNumber"] = "Número de série contém caracteres inválidos.";
    }

    if (!empty($ano_fabrico)) {
        if (!preg_match('/^\d{4}$/', $ano_fabrico) || (int)$ano_fabrico < 1900 || (int)$ano_fabrico > (int)date('Y')) {
            $erros["manufacturingYear"] = "Ano inválido. Deve ser entre 1900 e " . date('Y') . ".";
        }
    }

    if (!empty($data_aquisicao)) {
        $hoje = date('Y-m-d');
        if ($data_aquisicao > $hoje) {
            $erros["acquisitionDate"] = "A data de aquisição não pode ser no futuro.";
        }
    }

    if ($custo === '' || $custo === null) {
        $erros["cost"] = "Campo obrigatório.";
    } elseif (!is_numeric($custo) || (float)$custo < 0) {
        $erros["cost"] = "O custo deve ser um valor positivo.";
    }

    if (empty($erros)) {
 
        $designacao  = ucwords(strtolower($designacao));
        $marca       = ucwords(strtolower($marca));
        $modelo      = strtoupper($modelo);
        $fabricante  = ucwords(strtolower($fabricante));
        $categoria   = ucwords(strtolower($categoria));
        $custo       = !empty($custo) ? (float)$custo : null;
        $ano_fabrico = !empty($ano_fabrico) ? (int)$ano_fabrico : null;
        $data_aquisicao = !empty($data_aquisicao) ? $data_aquisicao : null;
 
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $ligacao->beginTransaction();
 
            $nome_servico = trim($_POST['servico'] ?? '');
            $nome_sala    = trim($_POST['sala']    ?? '');
 
            $id_servico = null;
            $id_sala    = null;
 
            if (!empty($nome_servico)) {
                $stmtServ = $ligacao->prepare("SELECT id_servico FROM servicos WHERE nome = :nome LIMIT 1");
                $stmtServ->execute([':nome' => $nome_servico]);
                $row = $stmtServ->fetch(PDO::FETCH_OBJ);
                if ($row) $id_servico = $row->id_servico;
            }
 
            if (!empty($nome_sala) && $id_servico) {
                $stmtSala = $ligacao->prepare("SELECT id_sala FROM salas WHERE identificacao = :ident AND id_servico = :id_serv LIMIT 1");
                $stmtSala->execute([':ident' => $nome_sala, ':id_serv' => $id_servico]);
                $row = $stmtSala->fetch(PDO::FETCH_OBJ);
                if ($row) $id_sala = $row->id_sala;
            }
 
            $id_fabricante = null;
            if (!empty($fabricante)) {
                $stmtFab = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
                $stmtFab->execute([':nome' => $fabricante]);
                $row = $stmtFab->fetch(PDO::FETCH_OBJ);
                if ($row) $id_fabricante = $row->id_fornecedor;
            }
 
            $sql = "INSERT INTO equipamentos (
                        codigo_interno, designacao, marca, modelo, numero_serie,
                        ano_fabrico, categoria, criticidade,
                        data_aquisicao, custo_aquisicao, tipo_entrada, estado,
                        falta_declaracao_ce, falta_manual_utilizador, falta_fatura_guia,
                        observacoes, id_servico, id_sala, id_fabricante
                    ) VALUES (
                        :codigo_interno, :designacao, :marca, :modelo, :numero_serie,
                        :ano_fabrico, :categoria, :criticidade,
                        :data_aquisicao, :custo_aquisicao, :tipo_entrada, :estado,
                        :falta_declaracao_ce, :falta_manual_utilizador, :falta_fatura_guia,
                        :observacoes, :id_servico, :id_sala, :id_fabricante
                    )";
 
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':codigo_interno'          => $codigo_interno,
                ':designacao'              => $designacao,
                ':marca'                   => $marca,
                ':modelo'                  => $modelo,
                ':numero_serie'            => $numero_serie,
                ':ano_fabrico'             => $ano_fabrico,
                ':categoria'               => $categoria,
                ':criticidade'             => $criticidade,
                ':data_aquisicao'          => $data_aquisicao,
                ':custo_aquisicao'         => $custo,
                ':tipo_entrada'            => $tipo_entrada,
                ':estado'                  => $estado,
                ':falta_declaracao_ce'     => $falta_declaracao_ce,
                ':falta_manual_utilizador' => $falta_manual_utilizador,
                ':falta_fatura_guia'       => $falta_fatura_guia,
                ':observacoes'             => $observacoes,
                ':id_servico'              => $id_servico,
                ':id_sala'                 => $id_sala,
                ':id_fabricante'           => $id_fabricante,
            ]);
 
            $id_equipamento = (int)$ligacao->lastInsertId();
 
            $nome_fornecedor  = trim($_POST['fornecedor']  ?? '');
            $nome_assistencia = trim($_POST['assistencia'] ?? '');
            $nome_consumiveis = trim($_POST['consumiveis'] ?? '');
 
            $papeisParaInserir = [];
            if (!empty($nome_fornecedor))  $papeisParaInserir[] = ['nome' => $nome_fornecedor,  'papel' => 'Comercial'];
            if (!empty($nome_assistencia)) $papeisParaInserir[] = ['nome' => $nome_assistencia, 'papel' => 'Assistência'];
            if (!empty($nome_consumiveis)) $papeisParaInserir[] = ['nome' => $nome_consumiveis, 'papel' => 'Consumíveis'];
 
            foreach ($papeisParaInserir as $entry) {
                $stmtF = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
                $stmtF->execute([':nome' => $entry['nome']]);
                $rowF = $stmtF->fetch(PDO::FETCH_OBJ);
                if ($rowF) {
                    $stmtEF = $ligacao->prepare("INSERT IGNORE INTO equipamento_fornecedor (id_equipamento, id_fornecedor, papel) VALUES (:id_eq, :id_f, :papel)");
                    $stmtEF->execute([':id_eq' => $id_equipamento, ':id_f' => $rowF->id_fornecedor, ':papel' => $entry['papel']]);
                }
            }
 
            $tem_garantia = isset($_POST['temGarantia']);
            $tem_contrato = isset($_POST['temContrato']);
 
            if ($tem_garantia) {
                $g_inicio = trim($_POST['garantiaInicio'] ?? '');
                $g_fim    = trim($_POST['garantiaFim']    ?? '');
                if (!empty($g_inicio) && !empty($g_fim)) {
                    $stmtGar = $ligacao->prepare("
                        INSERT INTO garantias_contratos (id_equipamento, tipo_cobertura, data_inicio, data_fim)
                        VALUES (:id_eq, 'Garantia', :inicio, :fim)
                    ");
                    $stmtGar->execute([':id_eq' => $id_equipamento, ':inicio' => $g_inicio, ':fim' => $g_fim]);
                }
            }
 
            if ($tem_contrato) {
                $c_ref    = trim($_POST['referenciaContrato']    ?? '');
                $c_entidade = trim($_POST['entidadeContrato']    ?? '');
                $c_tipo   = trim($_POST['tipoContrato']          ?? '');
                $c_period = trim($_POST['periodicidadeContrato'] ?? '');
                $c_inicio = trim($_POST['contratoInicio']        ?? '');
                $c_fim    = trim($_POST['contratoFim']           ?? '');
 
                $id_entidade = null;
                if (!empty($c_entidade)) {
                    $stmtEnt = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
                    $stmtEnt->execute([':nome' => $c_entidade]);
                    $rowEnt = $stmtEnt->fetch(PDO::FETCH_OBJ);
                    if ($rowEnt) $id_entidade = $rowEnt->id_fornecedor;
                }
 
                if (!empty($c_inicio) && !empty($c_fim)) {
                    $stmtCont = $ligacao->prepare("
                        INSERT INTO garantias_contratos
                            (id_equipamento, tipo_cobertura, referencia, id_entidade_responsavel, tipo_contrato, periodicidade, data_inicio, data_fim)
                        VALUES
                            (:id_eq, 'Contrato Manutenção', :ref, :id_ent, :tipo, :period, :inicio, :fim)
                    ");
                    $stmtCont->execute([
                        ':id_eq'  => $id_equipamento,
                        ':ref'    => $c_ref    ?: null,
                        ':id_ent' => $id_entidade,
                        ':tipo'   => $c_tipo   ?: null,
                        ':period' => $c_period ?: null,
                        ':inicio' => $c_inicio,
                        ':fim'    => $c_fim,
                    ]);
                }
            }
 
            $acessorios_post = $_POST['acessorios'] ?? [];
            foreach ($acessorios_post as $ace) {
                $ace_codigo     = trim($ace['codigo']     ?? '');
                $ace_designacao = trim($ace['designacao'] ?? '');
                $ace_serie      = trim($ace['serie']      ?? '');
                if (empty($ace_designacao)) continue;
 
                $stmtAce = $ligacao->prepare("
                    INSERT INTO acessorios (id_equipamento, codigo_componente, designacao, numero_serie)
                    VALUES (:id_eq, :cod, :des, :ser)
                ");
                $stmtAce->execute([
                    ':id_eq' => $id_equipamento,
                    ':cod'   => $ace_codigo     ?: null,
                    ':des'   => $ace_designacao,
                    ':ser'   => $ace_serie      ?: null,
                ]);
            }
 
            $docs_post = $_POST['docs'] ?? [];
            $pastas = [
                'Manual de utilizador'        => 'manuais',
                'Manual de serviço'           => 'manuais',
                'Certificado de calibração'   => 'certificados',
                'Contrato de manutenção'      => 'contratos',
                'Fatura ou guia de aquisição' => 'faturas',
                'Declaração de conformidade'  => 'declaracoes',
                'Certificado de Garantia'     => 'certificados',
                'Relatório técnico'           => 'certificados',
            ];
 
            $doc_index = 0;
            foreach ($docs_post as $idDoc => $doc) {
                $doc_tipo     = trim($doc['tipo']      ?? '');
                $doc_titulo   = trim($doc['titulo']    ?? '');
                $doc_emissao  = trim($doc['emissao']   ?? '');
                $doc_validade = trim($doc['validade']  ?? '');
                $doc_fornec   = trim($doc['fornecedor'] ?? '');
 
                if (empty($doc_tipo)) continue;
 
                $id_forn_doc = null;
                if (!empty($doc_fornec) && $doc_fornec !== 'Nenhuma') {
                    $stmtFD = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
                    $stmtFD->execute([':nome' => $doc_fornec]);
                    $rowFD = $stmtFD->fetch(PDO::FETCH_OBJ);
                    if ($rowFD) $id_forn_doc = $rowFD->id_fornecedor;
                }
 
                $caminho_ficheiro = 'assets/docs/sem_ficheiro.pdf'; // fallback
                if (isset($_FILES['docFicheiros']['tmp_name'][$doc_index]) && $_FILES['docFicheiros']['error'][$doc_index] === UPLOAD_ERR_OK) {
                    $pasta_tipo = $pastas[$doc_tipo] ?? 'outros';
                    $pasta_dest = __DIR__ . '/../../../../assets/docs/' . $pasta_tipo . '/';
 
                    if (!is_dir($pasta_dest)) mkdir($pasta_dest, 0755, true);
 
                    $nome_original = basename($_FILES['docFicheiros']['name'][$doc_index]);
                    $extensao      = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
                    $nome_seguro   = $codigo_interno . '_' . $pasta_tipo . '_' . ($doc_index + 1) . '.' . $extensao;
                    $caminho_full  = $pasta_dest . $nome_seguro;
 
                    if (move_uploaded_file($_FILES['docFicheiros']['tmp_name'][$doc_index], $caminho_full)) {
                        $caminho_ficheiro = 'assets/docs/' . $pasta_tipo . '/' . $nome_seguro;
                    }
                }
 
                $stmtDoc = $ligacao->prepare("
                    INSERT INTO documentos (id_equipamento, id_fornecedor, tipo_documento, titulo, data_emissao, data_validade, caminho_ficheiro)
                    VALUES (:id_eq, :id_forn, :tipo, :titulo, :emissao, :validade, :caminho)
                ");
                $stmtDoc->execute([
                    ':id_eq'    => $id_equipamento,
                    ':id_forn'  => $id_forn_doc,
                    ':tipo'     => $doc_tipo,
                    ':titulo'   => $doc_titulo   ?: null,
                    ':emissao'  => $doc_emissao  ?: null,
                    ':validade' => $doc_validade ?: null,
                    ':caminho'  => $caminho_ficheiro,
                ]);
 
                $doc_index++;
            }
 
            if ($id_servico) {
                $id_utilizador = $_SESSION['id_utilizador'] ?? null;
                $stmtHist = $ligacao->prepare("
                    INSERT INTO historico_movimentacoes (id_equipamento, id_servico_origem, id_servico_destino, motivo, id_utilizador)
                    VALUES (:id_eq, NULL, :id_serv, 'Entrada em inventário', :id_util)
                ");
                $stmtHist->execute([
                    ':id_eq'   => $id_equipamento,
                    ':id_serv' => $id_servico,
                    ':id_util' => $id_utilizador,
                ]);
            }
 
            $ligacao->commit();
            $ligacao = null;
 
            header("Location: lista_equi.php?sucesso=1");
            exit;
 
        } catch (PDOException $err) {
            if (isset($ligacao)) $ligacao->rollBack();
            if ($err->getCode() == 23000 && strpos($err->getMessage(), 'numero_serie') !== false) {
                $erros["serialNumber"] = "Este número de série já existe.";
            } else {
                $erro_sistema = "Erro ao guardar o equipamento: " . $err->getMessage();
            }
            $ligacao = null;
        }
    }
}
?>

<?php
try {
    $ligacao_codigo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    $stmt = $ligacao_codigo->query("SELECT codigo_interno FROM equipamentos WHERE codigo_interno LIKE 'EQ-%'");
    $todos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $maior = 0;
    foreach ($todos as $cod) {
        $partes = explode('-', $cod);
        if (count($partes) === 2 && is_numeric($partes[1])) {
            $num = (int)$partes[1];
            if ($num > $maior) $maior = $num;
        }
    }

    $proximo_codigo = "EQ-" . str_pad($maior + 1, 4, "0", STR_PAD_LEFT);
    $ligacao_codigo = null;
} catch (PDOException $e) {
    $proximo_codigo = "EQ-0001";
}
?>

<?php include '../../includes/header.php'; ?>

<?php include '../../includes/sidebar.php'; ?>

<main class="flex-grow-1 overflow-auto p-4 p-md-5">

    <!-- Cabeçalho Mobile -->
    <header class="d-md-none d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-stethoscope fs-5 text-brand"></i>
            <h1 class="h5 fw-bold mb-0 text-dark">MedStock</h1>
        </div>
        <button class="btn btn-light border-0 shadow-sm d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile"><i class="fa-solid fa-bars"></i></button>
    </header>

    <div class="d-flex align-items-center gap-3 mb-4" style="max-width: 1024px;">
        <a href="lista_equi.php"
            class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center"
            style="width: 36px; height: 36px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h1 class="h3 fw-bold text-dark mb-0">Novo Equipamento</h1>
            <p class="text-muted small mt-1 mb-0">Registe um novo equipamento no inventário</p>
        </div>
        <button type="button" id="btn-preencher-auto"
            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 shadow-sm"
            title="Preenche os campos com dados de demonstração">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Preencher Demo</span>
        </button>
    </div>

    <form action="#" method="POST" enctype="multipart/form-data" style="max-width: 1024px;" novalidate>

        <!-- SEPARADORES (WIZARD STEPS) -->
        <ul class="nav nav-tabs mb-4 border-bottom-0" id="equipamentoTabs" role="tablist"
            style="pointer-events: none;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-dark fw-medium" id="step1-tab" data-bs-target="#step1-pane"
                    type="button" role="tab" aria-selected="true">
                    <span class="badge bg-brand text-white me-1 rounded-pill">1</span> Identificação
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-muted fw-medium" id="step2-tab" data-bs-target="#step2-pane"
                    type="button" role="tab" aria-selected="false">
                    <span class="badge bg-secondary text-white me-1 rounded-pill">2</span> Receção
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-muted fw-medium" id="step3-tab" data-bs-target="#step3-pane"
                    type="button" role="tab" aria-selected="false">
                    <span class="badge bg-secondary text-white me-1 rounded-pill">3</span> Entidades e Contratos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-muted fw-medium" id="step4-tab" data-bs-target="#step4-pane"
                    type="button" role="tab" aria-selected="false">
                    <span class="badge bg-secondary text-white me-1 rounded-pill">4</span> Documentação
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-muted fw-medium" id="step5-tab" data-bs-target="#step5-pane"
                    type="button" role="tab" aria-selected="false">
                    <span class="badge bg-secondary text-white me-1 rounded-pill">5</span> Acessórios
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-muted fw-medium" id="step6-tab" data-bs-target="#step6-pane"
                    type="button" role="tab" aria-selected="false">
                    <span class="badge bg-secondary text-white me-1 rounded-pill">6</span> Observações
                </button>
            </li>
        </ul>

        <!-- CONTEÚDO DOS PASSOS -->
        <div class="card dash-card mb-4 p-0">
            <div class="tab-content" id="equipamentoTabsContent">

                <!-- PASSO 1: IDENTIFICAÇÃO -->
                <div class="tab-pane fade show active" id="step1-pane" role="tabpanel" tabindex="0">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Detalhes de Identificação
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-medium mb-1">Código Interno <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.7rem;" title="Gerado automaticamente pelo sistema"></i></label>
                                <input type="text" id="equipamentoCodigoPrincipal" name="internalCode" class="form-control shadow-sm bg-light fw-bold text-muted" value="<?= htmlspecialchars($proximo_codigo) ?>" readonly>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label small fw-medium mb-1">Designação *</label>
                                <input type="text" name="name" class="form-control shadow-sm"
                                    placeholder="Ex: Monitor Multiparamétrico..."
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório ou muito curto.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-medium mb-1">Marca *</label>
                                <input type="text" name="brand" class="form-control shadow-sm"
                                    placeholder="Ex: Philips"
                                    value="<?= htmlspecialchars($_POST['brand'] ?? '') ?>" required>
                                <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium mb-1">Modelo *</label>
                                <input type="text" name="model" class="form-control shadow-sm"
                                    placeholder="Ex: IntelliVue MX700"
                                    value="<?= htmlspecialchars($_POST['model'] ?? '') ?>" required>
                                <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium mb-1">Número de Série *</label>
                                <input type="text" name="serialNumber" class="form-control shadow-sm"
                                    placeholder="SN-XXXX-0000"
                                    value="<?= htmlspecialchars($_POST['serialNumber'] ?? '') ?>" required>
                                <div class="invalid-feedback" id="feedbackSerial" style="font-size: 0.70rem;">Campo
                                    obrigatório.</div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small fw-medium mb-1">Fabricante *</label>
                                <div class="dropdown">
                                    <input type="hidden" name="manufacturer" id="inputManufacturer" required>
                                    <button
                                        class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm bg-white"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-display="static">
                                        <span id="textManufacturer" class="text-muted">Selecionar
                                            fabricante...</span>
                                    </button>
                                    <ul
                                        class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                        <li class="px-2 pb-2 mb-2 border-bottom">
                                            <input type="text"
                                                class="form-control form-control-sm shadow-none bg-light"
                                                id="searchManufacturer" placeholder="Pesquisar..."
                                                onkeyup="filtrarDropdown('searchManufacturer', 'listaManufacturer')"
                                                onclick="event.stopPropagation()">
                                        </li>
                                        <div id="listaManufacturer">
                                        </div>
                                    </ul>
                                    <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium mb-1">Ano de Fabrico</label>
                                <input type="number" name="manufacturingYear"
                                    class="form-control form-control-sm shadow-sm" placeholder="Ex: 2024"
                                    min="1900"
                                    value="<?= htmlspecialchars($_POST['manufacturingYear'] ?? '') ?>">
                                <div class="invalid-feedback" id="feedbackAno" style="font-size: 0.70rem;">Data
                                    inválida.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium mb-1">Categoria *</label>
                                <div class="dropdown">
                                    <input type="hidden" name="categoria" id="inputCategoria" required>
                                    <button
                                        class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-display="static">
                                        <span id="textCategoria" class="text-muted">Selecionar categoria...</span>
                                    </button>
                                    <ul
                                        class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                        <li class="px-2 pb-2 mb-2 border-bottom">
                                            <input type="text"
                                                class="form-control form-control-sm shadow-none bg-light"
                                                id="searchCategoria" placeholder="Escreva para pesquisar..."
                                                onkeyup="filtrarDropdown('searchCategoria', 'listaCategoria')"
                                                onclick="event.stopPropagation()">
                                        </li>
                                        <div id="listaCategoria">
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Monitorização')">Monitorização</a>
                                            </li>
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Suporte de vida')">Suporte de vida</a>
                                            </li>
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Terapia')">Terapia</a>
                                            </li>
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Esterilização')">Esterilização</a>
                                            </li>
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Laboratório')">Laboratório</a>
                                            </li>
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Diagnóstico por Imagem')">Diagnóstico
                                                    por Imagem</a></li>
                                            <li><a class="dropdown-item py-2" href="#"
                                                    onclick="selecionarDropdown('categoria', 'Reabilitação')">Reabilitação</a>
                                            </li>
                                        </div>
                                    </ul>
                                    <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium mb-1">Criticidade Clínica *</label>
                                <div class="dropdown">
                                    <input type="hidden" name="criticidade" id="inputCriticidade" required>
                                    <button
                                        class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-display="static">
                                        <span id="textCriticidade" class="text-muted">Selecionar
                                            criticidade...</span>
                                    </button>
                                    <ul
                                        class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                        <li><a class="dropdown-item py-2" href="#"
                                                onclick="selecionarDropdown('criticidade', 'Baixa')">Baixa</a></li>
                                        <li><a class="dropdown-item py-2" href="#"
                                                onclick="selecionarDropdown('criticidade', 'Média')">Média</a></li>
                                        <li><a class="dropdown-item py-2" href="#"
                                                onclick="selecionarDropdown('criticidade', 'Alta')">Alta</a></li>
                                        <li><a class="dropdown-item py-2" href="#"
                                                onclick="selecionarDropdown('criticidade', 'Suporte de Vida')">Suporte de Vida</a></li>
                                    </ul>
                                    <div class="invalid-feedback" style="font-size: 0.70rem;">Campo obrigatório.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="alertaPasso1" class="alert alert-danger p-2 mt-4 mb-0 shadow-sm <?= (isset($erros["name"]) || isset($erros["brand"]) || isset($erros["model"]) || isset($erros["serialNumber"]) || isset($erros["manufacturer"]) || isset($erros["categoria"]) || isset($erros["criticidade"]) || isset($erros["manufacturingYear"])) ? '' : 'd-none' ?>" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Foram encontrados erros:</strong>
                            <ul class="mb-0 mt-1">
                                <?php foreach (["name", "brand", "model", "serialNumber", "manufacturer", "categoria", "criticidade", "manufacturingYear"] as $campo): ?>
                                    <?php if (isset($erros[$campo])): ?>
                                        <li><?= htmlspecialchars($erros[$campo]) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                    <div class="card-footer bg-light p-3 border-top d-flex justify-content-end">
                        <button type="button" class="btn btn-brand px-4" data-bs-wizard-step="#step2-tab">
                            Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 2: FABRICANTE -->
                <div class="tab-pane fade" id="step2-pane" role="tabpanel" tabindex="0">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Receção & Localização</h5>

                        <div class="row g-5">
                            <div class="col-lg-6 border-end">
                                <h6 class="fw-bold text-dark mb-4"></i> Entrada e Estado</h6>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium mb-1">Data de Aquisição</label>
                                        <input type="date" name="acquisitionDate" id="dataAquisicao"
                                            class="form-control shadow-sm"
                                            value="<?= htmlspecialchars($_POST['acquisitionDate'] ?? '') ?>">
                                        <div class="invalid-feedback" id="feedbackDataAquisicao" style="font-size: 0.70rem;">Data inválida.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium mb-1">Custo (EUR) *</label>
                                        <input type="number" name="cost" class="form-control shadow-sm"
                                            placeholder="0.00" step="0.01" min="0"
                                            value="<?= htmlspecialchars($_POST['cost'] ?? '') ?>" required>
                                        <div class="invalid-feedback" id="feedbackCusto"
                                            style="font-size: 0.70rem;">Campo obrigatório ou custo inválido.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium mb-1">Tipo de Entrada *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="entryType" id="inputEntryType" required>
                                            <button
                                                class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textEntryType" class="text-muted">Selecionar
                                                    entrada...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('entryType', 'Compra')">Compra</a>
                                                </li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('entryType', 'Doação')">Doação</a>
                                                </li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('entryType', 'Aluguer')">Aluguer</a>
                                                </li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('entryType', 'Empréstimo')">Empréstimo</a>
                                                </li>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium mb-1">Estado Atual *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="status" id="inputStatus" required>
                                            <button
                                                class="form-select text-start d-flex justify-content-between align-items-center shadow-sm border-warning bg-warning bg-opacity-10"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textStatus" class="text-muted">Selecionar estado...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('status', 'Em Quarentena')">Em Quarentena</a></li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('status', 'Ativo')">Ativo</a></li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('status', 'Em Calibração')">Em Calibração</a></li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('status', 'Em Manutenção')">Em Manutenção</a></li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('status', 'Inativo')">Inativo</a></li>
                                                <li><a class="dropdown-item py-2" href="#"
                                                        onclick="selecionarDropdown('status', 'Abatido')">Abatido</a></li>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                        <div class="form-text" style="font-size: 0.65rem;">As entradas novas ficam
                                            em quarentena até teste elétrico.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="fw-bold text-dark mb-4"></i> Localização Hierárquica</h6>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium mb-1">Edifício *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="edificio" id="inputEdificio" required>
                                            <button id="btnEdificio"
                                                class="form-select text-start d-flex justify-content-between align-items-center shadow-sm"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textEdificio" class="text-muted">Selecionar
                                                    edifício...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchEdificio" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchEdificio', 'listaEdificio')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaEdificio">
                                                </div>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium mb-1">Piso *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="piso" id="inputPiso" required>
                                            <button id="btnPiso"
                                                class="form-select text-start d-flex justify-content-between align-items-center shadow-sm disabled"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textPiso" class="text-muted">Aguardar edifício...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchPiso" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchPiso', 'listaPiso')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaPiso">
                                                </div>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-medium mb-1">Serviço *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="servico" id="inputServico" required>
                                            <button id="btnServico"
                                                class="form-select text-start d-flex justify-content-between align-items-center shadow-sm disabled"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textServico" class="text-muted">Aguardar piso...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchServico" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchServico', 'listaServico')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaServico">
                                                </div>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-medium mb-1">Sala / Compartimento </label>
                                        <div class="dropdown">
                                            <input type="hidden" name="sala" id="inputSala">
                                            <button id="btnSala"
                                                class="form-select text-start d-flex justify-content-between align-items-center shadow-sm disabled"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textSala" class="text-muted">Aguardar serviço...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchSala" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchSala', 'listaSala')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaSala">
                                                </div>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-danger p-2 mt-4 mb-0 shadow-sm <?= (isset($erros["cost"]) || isset($erros["entryType"]) || isset($erros["status"]) || isset($erros["acquisitionDate"])) ? '' : 'd-none' ?>" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Foram encontrados erros:</strong>
                            <ul class="mb-0 mt-1">
                                <?php foreach (["acquisitionDate", "cost", "entryType", "status"] as $campo): ?>
                                    <?php if (isset($erros[$campo])): ?>
                                        <li><?= htmlspecialchars($erros[$campo]) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                    <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-wizard-step="#step1-tab">
                            <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-brand px-4 shadow-sm" data-bs-wizard-step="#step3-tab">
                            Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 3 -->
                <div class="tab-pane fade" id="step3-pane" role="tabpanel" tabindex="0">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Contratos & Entidades
                            Associadas</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-light">
                                    <h6 class="fw-bold text-dark mb-3"><i
                                            class="fa-solid fa-truck text-muted me-2"></i> Entidades</h6>

                                    <div class="mb-3">
                                        <label class="form-label small fw-medium mb-1">Fornecedor Comercial
                                            *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="fornecedor" id="inputFornecedor" required>
                                            <button
                                                class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textFornecedor" class="text-muted">Selecionar
                                                    fornecedor...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchFornecedor" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchFornecedor', 'listaFornecedor')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaFornecedor">
                                                </div>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-medium mb-1">Assistência Técnica Oficial
                                            *</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="assistencia" id="inputAssistencia" required>
                                            <button
                                                class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textAssistencia" class="text-muted">Selecionar
                                                    assistência...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchAssistencia" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchAssistencia', 'listaAssistencia')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaAssistencia">
                                                </div>
                                            </ul>
                                            <div class="invalid-feedback" style="font-size: 0.70rem;">Campo
                                                obrigatório.</div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label small fw-medium mb-1">Fornecedor de Consumíveis
                                            (Opcional)</label>
                                        <div class="dropdown">
                                            <input type="hidden" name="consumiveis" id="inputConsumiveis">
                                            <button
                                                class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-display="static">
                                                <span id="textConsumiveis" class="text-muted">Selecionar
                                                    fornecedor...</span>
                                            </button>
                                            <ul
                                                class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                <li class="px-2 pb-2 mb-2 border-bottom">
                                                    <input type="text"
                                                        class="form-control form-control-sm shadow-none bg-light"
                                                        id="searchConsumiveis" placeholder="Pesquisar..."
                                                        onkeyup="filtrarDropdown('searchConsumiveis', 'listaConsumiveis')"
                                                        onclick="event.stopPropagation()">
                                                </li>
                                                <div id="listaConsumiveis">
                                                </div>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div
                                    class="p-3 border border-success-subtle bg-success bg-opacity-10 rounded-3 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-dark mb-0"><i
                                                class="fa-solid fa-shield-halved text-success me-2"></i> Cobertura
                                            Legal</h6>
                                    </div>

                                    <div class="mb-2 pb-2 border-bottom border-success-subtle">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="temGarantia"
                                                onchange="toggleCampos('temGarantia', 'camposGarantia')" checked>
                                            <label class="form-check-label fw-bold text-dark small"
                                                for="temGarantia">Dentro da Garantia Legal</label>
                                        </div>

                                        <div id="camposGarantia" class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-medium text-dark mb-1">Início da
                                                    Garantia *</label>
                                                <input type="date" name="garantiaInicio"
                                                    class="form-control shadow-sm form-control-sm bg-white"
                                                    required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                    Obrigatório.</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-medium text-dark mb-1">Fim da
                                                    Garantia *</label>
                                                <input type="date" name="garantiaFim"
                                                    class="form-control shadow-sm form-control-sm border-warning bg-white"
                                                    required>
                                                <div class="invalid-feedback" id="erroDataGarantia"
                                                    style="font-size: 0.70rem;">Inválido.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="temContrato"
                                                onchange="toggleCampos('temContrato', 'camposContrato')" checked>
                                            <label class="form-check-label fw-bold text-dark small"
                                                for="temContrato">Possui Contrato de Manutenção</label>
                                        </div>

                                        <div id="camposContrato" class="row g-2">
                                            <div class="col-5">
                                                <label class="form-label small text-dark mb-1">Nº Referência
                                                    *</label>
                                                <input type="text" name="referenciaContrato"
                                                    class="form-control shadow-sm form-control-sm bg-white"
                                                    placeholder="Ex: CNT-2024" required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                    Obrigatório.</div>
                                            </div>
                                            <div class="col-7">
                                                <label class="form-label small text-dark mb-1">Entidade Responsável
                                                    *</label>
                                                <div class="dropdown">
                                                    <input type="hidden" name="entidadeContrato"
                                                        id="inputEntidadeContrato" required>
                                                    <button
                                                        class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm bg-white"
                                                        type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false" data-bs-display="static">
                                                        <span id="textEntidadeContrato"
                                                            class="text-muted">Selecionar entidade...</span>
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                                        <li class="px-2 pb-2 mb-2 border-bottom">
                                                            <input type="text"
                                                                class="form-control form-control-sm shadow-none bg-light"
                                                                id="searchEntidadeContrato"
                                                                placeholder="Pesquisar..."
                                                                onkeyup="filtrarDropdown('searchEntidadeContrato', 'listaEntidadeContrato')"
                                                                onclick="event.stopPropagation()">
                                                        </li>
                                                        <div id="listaEntidadeContrato">
                                                        </div>
                                                    </ul>
                                                    <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                        Obrigatório.</div>
                                                </div>
                                            </div>

                                            <div class="col-6 mt-1">
                                                <label class="form-label small text-dark mb-1">Tipo de Contrato
                                                    *</label>
                                                <select name="tipoContrato"
                                                    class="form-select form-select-sm shadow-sm text-dark bg-white"
                                                    required>
                                                    <option value="" selected disabled>Selecione...</option>
                                                    <option value="Full-Service">Full-Service</option>
                                                    <option value="Apenas Preventiva">Apenas Preventiva</option>
                                                    <option value="Peças e Mão de Obra">Peças e Mão de Obra</option>
                                                </select>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                    Obrigatório.</div>
                                            </div>
                                            <div class="col-6 mt-1">
                                                <label class="form-label small text-dark mb-1">Periodicidade
                                                    *</label>
                                                <select name="periodicidadeContrato"
                                                    class="form-select form-select-sm shadow-sm text-dark bg-white"
                                                    required>
                                                    <option value="" selected disabled>Selecione...</option>
                                                    <option value="Anual">Anual</option>
                                                    <option value="Semestral">Semestral</option>
                                                    <option value="Trimestral">Trimestral</option>
                                                </select>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                    Obrigatório.</div>
                                            </div>
                                            <div class="col-6 mt-1">
                                                <label class="form-label small text-dark mb-1">Início do Contrato
                                                    *</label>
                                                <input type="date" name="contratoInicio"
                                                    class="form-control shadow-sm form-control-sm bg-white"
                                                    required>
                                                <div class="invalid-feedback" style="font-size: 0.70rem;">
                                                    Obrigatório.</div>
                                            </div>
                                            <div class="col-6 mt-1">
                                                <label class="form-label small text-dark mb-1">Fim do Contrato
                                                    *</label>
                                                <input type="date" name="contratoFim"
                                                    class="form-control shadow-sm form-control-sm border-warning bg-white"
                                                    required>
                                                <div class="invalid-feedback" id="erroDataContrato"
                                                    style="font-size: 0.70rem;">Inválido.</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="alert alert-danger p-2 text-center mt-4 mb-0 shadow-sm d-none" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Erro:</strong> Por favor,
                            verifique os campos a vermelho antes de avançar.
                        </div>

                    </div>
                    <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-wizard-step="#step2-tab">
                            <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-brand px-4 shadow-sm" data-bs-wizard-step="#step4-tab">
                            Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 4: Documentação -->
                <div class="tab-pane fade" id="step4-pane" role="tabpanel" tabindex="0">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-semibold fs-6 mb-3 text-dark border-bottom pb-2">Documentação Técnica e Legal
                        </h5>

                        <div class="mb-4 p-3 border border-warning-subtle bg-warning bg-opacity-10 rounded-3">
                            <label class="form-label small fw-bold text-dark mb-2"><i
                                    class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Documentação
                                Obrigatória em Falta</label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input border-warning" type="checkbox" id="faltaCE"
                                        name="faltaCE" value="Declaração CE">
                                    <label class="form-check-label small fw-medium text-dark"
                                        for="faltaCE">Declaração CE</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input border-warning" type="checkbox" id="faltaManual"
                                        name="faltaManual" value="Manual Utilizador">
                                    <label class="form-check-label small fw-medium text-dark"
                                        for="faltaManual">Manual de Utilizador</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input border-warning" type="checkbox" id="faltaFatura"
                                        name="faltaFatura" value="Fatura">
                                    <label class="form-check-label small fw-medium text-dark"
                                        for="faltaFatura">Fatura / Guia</label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 border mb-4">
                            <h6 class="fw-bold text-dark small mb-3 border-bottom pb-2">Novo Documento</h6>

                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small text-dark mb-1">Tipo de Documento *</label>
                                    <div class="dropdown">
                                        <input type="hidden" name="tipoDocumento" id="inputTipoDocumento">
                                        <button id="btnTipoDocumento"
                                            class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm bg-white"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            data-bs-display="static">
                                            <span id="textTipoDocumento" class="text-muted">Selecionar
                                                tipo...</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1">
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Manual de utilizador'); verificarValidadeDoc('Manual de utilizador')">Manual
                                                    de utilizador</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Manual de serviço'); verificarValidadeDoc('Manual de serviço')">Manual
                                                    de serviço</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Certificado de calibração'); verificarValidadeDoc('Certificado de calibração')">Certificado
                                                    de calibração</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Contrato de manutenção'); verificarValidadeDoc('Contrato de manutenção')">Contrato
                                                    de manutenção</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Fatura ou guia de aquisição'); verificarValidadeDoc('Fatura ou guia de aquisição')">Fatura
                                                    ou guia de aquisição</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Declaração de conformidade'); verificarValidadeDoc('Declaração de conformidade')">Declaração
                                                    de conformidade</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Certificado de Garantia'); verificarValidadeDoc('Certificado de Garantia')">Certificado
                                                    de Garantia</a></li>
                                            <li><a class="dropdown-item py-1 small" href="#"
                                                    onclick="selecionarDropdown('tipoDocumento', 'Relatório técnico'); verificarValidadeDoc('Relatório técnico')">Relatório
                                                    técnico</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-dark mb-1">Título do Documento
                                        (Opcional)</label>
                                    <input type="text" id="docTitulo"
                                        class="form-control form-control-sm shadow-sm bg-white"
                                        placeholder="Ex: Manual de Operação v2.0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-dark mb-1">Entidade / Fornecedor
                                        (Opcional)</label>
                                    <div class="dropdown">
                                        <input type="hidden" name="docFornecedor" id="inputDocFornecedor">
                                        <button
                                            class="form-select form-select-sm text-start d-flex justify-content-between align-items-center shadow-sm bg-white"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            data-bs-display="static">
                                            <span id="textDocFornecedor" class="text-muted">Nenhuma</span>
                                        </button>
                                        <ul
                                            class="dropdown-menu w-100 shadow-sm border-0 mt-1 dropdown-menu-scrollable">
                                            <li class="px-2 pb-2 mb-2 border-bottom">
                                                <input type="text"
                                                    class="form-control form-control-sm shadow-none bg-light"
                                                    id="searchDocFornecedor" placeholder="Pesquisar..."
                                                    onkeyup="filtrarDropdown('searchDocFornecedor', 'listaDocFornecedor')"
                                                    onclick="event.stopPropagation()">
                                            </li>
                                            <div id="listaDocFornecedor">
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small text-dark mb-1">Data Emissão</label>
                                    <input type="date" id="docEmissao"
                                        class="form-control form-control-sm shadow-sm bg-white">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-dark mb-1">Validade <i
                                            class="fa-solid fa-circle-info text-muted ms-1"
                                            title="Aplicável a Certificados de Calibração"></i></label>
                                    <input type="date" id="dataValidadeDoc"
                                        class="form-control form-control-sm shadow-sm" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-dark mb-1">Ficheiro (PDF, JPG) *</label>
                                    <input type="file" id="docFicheiro" name="docFicheiros[]"
    class="form-control form-control-sm shadow-sm bg-white"
    accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="btnAnexarDoc"
                                        class="btn btn-sm btn-brand fw-bold shadow-sm w-100">
                                        <i class="fa-solid fa-plus me-1"></i> Anexar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 overflow-hidden shadow-sm">
                            <div class="table-responsive bg-white" style="max-height: 180px; overflow-y: auto;">
                                <table class="table table-hover mb-0 text-start align-middle"
                                    style="position: relative;">
                                    <thead class="table-light text-muted small text-uppercase sticky-top"
                                        style="font-size: 0.75rem; z-index: 1;">
                                        <tr>
                                            <th class="fw-semibold px-3 py-2 border-bottom">Documento</th>
                                            <th class="fw-semibold py-2 border-bottom">Validade</th>
                                            <th class="fw-semibold py-2 border-bottom">Ficheiro</th>
                                            <th class="fw-semibold text-end px-3 py-2 border-bottom">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelaDocsBody">
                                        <tr id="emptyDocRow">
                                            <td colspan="4" class="text-center text-muted small py-4">
                                                <i
                                                    class="fa-regular fa-folder-open fs-4 d-block mb-2 text-secondary"></i>
                                                Nenhum documento anexado ainda.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="alertaPasso4" class="alert alert-danger p-2 text-center mt-4 mb-0 shadow-sm d-none"
                            role="alert">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Atenção:</strong> <span
                                id="textoAlertaPasso4">Falta anexar documentação obrigatória.</span>
                        </div>

                    </div>
                    <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-wizard-step="#step3-tab">
                            <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-brand px-4 shadow-sm fw-bold"
                            data-bs-wizard-step="#step5-tab">
                            Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 5: Acessorios -->
                <div class="tab-pane fade" id="step5-pane" role="tabpanel" tabindex="0">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Componentes e Acessórios Associados</h5>

                        <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                            <div class="col-md-3">
                                <label class="form-label small fw-medium mb-1">Cód. Componente <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.7rem;" title="Gerado automaticamente"></i></label>
                                <input type="text" id="acessorioCodigo" class="form-control form-control-sm shadow-sm bg-light fw-bold text-muted" value="EQ-0002.01" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium mb-1">Designação do Acessório *</label>
                                <input type="text" id="acessorioDesignacao" class="form-control form-control-sm shadow-sm bg-white" placeholder="Ex: Sonda Linear">
                                <div class="invalid-feedback" style="font-size: 0.70rem;">Obrigatório.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-medium mb-1">Nº Série / Lote (Opcional)</label>
                                <input type="text" id="acessorioSerie" class="form-control form-control-sm shadow-sm bg-white" placeholder="Ex: SN-889900">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="btnAdicionarAcessorio" class="btn btn-sm btn-brand fw-bold shadow-sm w-100">
                                    <i class="fa-solid fa-plus me-1"></i> Adicionar
                                </button>
                            </div>
                        </div>

                        <div class="border rounded-3 overflow-hidden shadow-sm">
                            <div class="table-responsive bg-white" style="max-height: 180px; overflow-y: auto;">
                                <table class="table table-hover mb-0 text-start align-middle" style="position: relative;">
                                    <thead class="table-light text-muted small text-uppercase sticky-top" style="font-size: 0.75rem; z-index: 1;">
                                        <tr>
                                            <th class="fw-semibold px-3 py-2 border-bottom">Código</th>
                                            <th class="fw-semibold py-2 border-bottom">Designação</th>
                                            <th class="fw-semibold py-2 border-bottom">Referência / Nº Série</th>
                                            <th class="fw-semibold text-end px-3 py-2 border-bottom">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelaAcessoriosBody">
                                        <tr id="emptyAcessorioRow">
                                            <td colspan="4" class="text-center text-muted small py-4">
                                                <i class="fa-solid fa-puzzle-piece fs-4 d-block mb-2 text-secondary"></i>
                                                Nenhum componente associado.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-wizard-step="#step4-tab">
                            <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-brand px-4 shadow-sm fw-bold" data-bs-wizard-step="#step6-tab">
                            Próximo <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 6: OBSERVAÇÕES -->
                <div class="tab-pane fade" id="step6-pane" role="tabpanel" tabindex="0">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold fs-6 mb-4 text-dark border-bottom pb-2">Notas e Observações</h5>

                        <div class="alert alert-secondary border-0 small mb-4">
                            Utilize este espaço para registar qualquer anomalia detetada na receção, detalhes
                            específicos sobre a instalação ou recomendações para os utilizadores. (Opcional)
                        </div>

                        <textarea name="observations" class="form-control shadow-sm" rows="6"
                            placeholder="Insira notas adicionais sobre o equipamento, detalhes de instalação, ou informações relevantes..."></textarea>

                        <?php if (!empty($erro_sistema)): ?>
                            <div class="alert alert-danger p-3 mt-4 mb-0 shadow-sm" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                <strong>Erro ao guardar:</strong>
                                <p class="mb-0 mt-1 small"><?= htmlspecialchars($erro_sistema) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer bg-light p-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-wizard-step="#step5-tab">
                            <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                        </button>

                        <button type="submit" id="btnRegistarEquipamento"
                            class="btn btn-brand px-4 shadow-sm fw-bold">
                            <i class="fa-solid fa-save me-1"></i> Registar Equipamento
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const campoData = document.getElementById('dataAquisicao');
        if (campoData && !campoData.value) {
            const hoje = new Date().toISOString().split('T')[0];
            campoData.value = hoje;
        }

        // LÓGICA DO BOTÃO "ANEXAR" (PASSO 4)
        const btnAnexar = document.getElementById('btnAnexarDoc');
        if (btnAnexar) {
            btnAnexar.addEventListener('click', function() {
                const tipo = document.getElementById('inputTipoDocumento').value;
                const titulo = document.getElementById('docTitulo').value;
                const emissao = document.getElementById('docEmissao').value;
                const validade = document.getElementById('dataValidadeDoc').value;
                const ficheiroInput = document.getElementById('docFicheiro');
                const ficheiro = ficheiroInput.value;

                if (!tipo || !ficheiro) {
                    alert("Por favor, selecione o Tipo de Documento e escolha um Ficheiro para anexar.");
                    return;
                }

                if (document.getElementById('dataValidadeDoc').disabled === false && !validade) {
                    alert("Atenção: Para certificados de calibração, a Data de Validade é obrigatória!");
                    return;
                }

                if (emissao && validade) {
                    if (new Date(validade) < new Date(emissao)) {
                        alert("Atenção: A Data de Validade não pode ser anterior à Data de Emissão.");
                        return; // Interrompe e não adiciona à tabela
                    }
                }

                // Limpar a linha de "Nenhum documento"
                const tbody = document.getElementById('tabelaDocsBody');
                const emptyRow = document.getElementById('emptyDocRow');
                if (emptyRow) emptyRow.remove();

                const fileName = ficheiro.split('\\').pop();

                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td class="px-3 py-2 small fw-medium text-dark">
                            <span class="badge bg-secondary mb-1 d-inline-block tipo-doc-anexado">${tipo}</span><br>${titulo || 'Documento Base'}
                        </td>
                        <td class="py-2 small ${validade ? 'text-warning fw-bold' : 'text-muted'}">${validade || 'N/A'}</td>
                        <td class="py-2 small text-muted"><i class="fa-solid fa-file-pdf text-danger me-1"></i> ${fileName}</td>
                        <td class="text-end px-3 py-2"><button type="button" class="btn btn-sm text-danger btn-remover-doc"><i class="fa-solid fa-trash-can"></i></button></td>
                    `;
                tbody.appendChild(tr);

                const idDoc = Date.now();
                const form = document.querySelector('form');

                const hTipo = document.createElement('input');
                hTipo.type = 'hidden';
                hTipo.name = `docs[${idDoc}][tipo]`;
                hTipo.value = tipo;
                form.appendChild(hTipo);

                const hTitulo = document.createElement('input');
                hTitulo.type = 'hidden';
                hTitulo.name = `docs[${idDoc}][titulo]`;
                hTitulo.value = titulo;
                form.appendChild(hTitulo);

                const hEmissao = document.createElement('input');
                hEmissao.type = 'hidden';
                hEmissao.name = `docs[${idDoc}][emissao]`;
                hEmissao.value = emissao;
                form.appendChild(hEmissao);

                const hValidade = document.createElement('input');
                hValidade.type = 'hidden';
                hValidade.name = `docs[${idDoc}][validade]`;
                hValidade.value = validade;
                form.appendChild(hValidade);

                const hFornDoc = document.createElement('input');
                hFornDoc.type = 'hidden';
                hFornDoc.name = `docs[${idDoc}][fornecedor]`;
                hFornDoc.value = document.getElementById('inputDocFornecedor').value || '';
                form.appendChild(hFornDoc);

                tr.dataset.docId = idDoc;

                tr.querySelector('.btn-remover-doc').addEventListener('click', function() {
                    const docId = tr.dataset.docId;
                    if (docId) {
                        document.querySelectorAll(`input[name^="docs[${docId}]"]`).forEach(i => i.remove());
                    }
                    tr.remove();
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `<tr id="emptyDocRow"><td colspan="4" class="text-center text-muted small py-4"><i class="fa-regular fa-folder-open fs-4 d-block mb-2 text-secondary"></i>Nenhum documento anexado ainda.</td></tr>`;
                    }
                });

                document.getElementById('docTitulo').value = '';
                document.getElementById('docEmissao').value = '';
                document.getElementById('dataValidadeDoc').value = '';
                ficheiroInput.value = '';
                desativarNivelLocalizacao('tipoDocumento', 'Selecionar tipo...', false);
                document.getElementById('dataValidadeDoc').disabled = true;
                document.getElementById('dataValidadeDoc').classList.remove('bg-white', 'border-warning');
                document.getElementById('dataValidadeDoc').classList.add('bg-light');
            });
        }

        // LÓGICA DO BOTÃO "ADICIONAR ACESSÓRIO" (PASSO 5) - COM AUTO-INCREMENTO E LEITURA DINÂMICA

        function atualizarCodigoNovoAcessorio() {
            const tbody = document.getElementById('tabelaAcessoriosBody');
            let numeros = [];

            for (let tr of tbody.children) {
                if (tr.id !== 'emptyAcessorioRow') {
                    const tdCode = tr.querySelector('td:first-child');
                    if (tdCode && tdCode.innerText.includes('.')) {
                        const partes = tdCode.innerText.split('.');
                        if (partes.length === 2) {
                            numeros.push(parseInt(partes[1], 10));
                        }
                    }
                }
            }

            numeros.sort((a, b) => a - b);

            let proximoNum = 1;
            for (let i = 0; i < numeros.length; i++) {
                if (numeros[i] === proximoNum) {
                    proximoNum++; // O número existe, vamos testar o próximo
                } else if (numeros[i] > proximoNum) {
                    break; // Encontrámos um buraco!
                }
            }

            const proximoNumStr = proximoNum.toString().padStart(2, '0');
            const codigoPrincipal = document.getElementById('equipamentoCodigoPrincipal').value || 'EQ-0002';

            document.getElementById('acessorioCodigo').value = `${codigoPrincipal}.${proximoNumStr}`;
        }

        const btnAdicionarAcessorio = document.getElementById('btnAdicionarAcessorio');
        if (btnAdicionarAcessorio) {
            atualizarCodigoNovoAcessorio();

            btnAdicionarAcessorio.addEventListener('click', function() {
                const codigoInput = document.getElementById('acessorioCodigo');
                const designacaoInput = document.getElementById('acessorioDesignacao');
                const serieInput = document.getElementById('acessorioSerie');

                const codigo = codigoInput.value;
                const designacao = designacaoInput.value.trim();
                const serie = serieInput.value.trim();

                if (!designacao) {
                    designacaoInput.classList.add('is-invalid', 'border-danger');
                    return;
                } else {
                    designacaoInput.classList.remove('is-invalid', 'border-danger');
                }

                const tbody = document.getElementById('tabelaAcessoriosBody');
                const emptyRow = document.getElementById('emptyAcessorioRow');
                if (emptyRow) emptyRow.remove();

                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td class="px-3 py-2 small fw-bold text-brand">${codigo}</td>
                        <td class="py-2 small fw-medium text-dark">${designacao}</td>
                        <td class="py-2 small text-muted">${serie || '<span class="fst-italic text-secondary">Não definido</span>'}</td>
                        <td class="text-end px-3 py-2">
                            <button type="button" class="btn btn-sm text-danger btn-remover-acessorio"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    `;
                tbody.appendChild(tr);

                const idAce = Date.now();
                const form = document.querySelector('form');

                const hCod = document.createElement('input');
                hCod.type = 'hidden';
                hCod.name = `acessorios[${idAce}][codigo]`;
                hCod.value = codigo;
                form.appendChild(hCod);

                const hDes = document.createElement('input');
                hDes.type = 'hidden';
                hDes.name = `acessorios[${idAce}][designacao]`;
                hDes.value = designacao;
                form.appendChild(hDes);

                const hSer = document.createElement('input');
                hSer.type = 'hidden';
                hSer.name = `acessorios[${idAce}][serie]`;
                hSer.value = serie;
                form.appendChild(hSer);

                tr.dataset.aceId = idAce;

                tr.querySelector('.btn-remover-acessorio').addEventListener('click', function() {
                    const aceId = tr.dataset.aceId;
                    if (aceId) {
                        document.querySelectorAll(`input[name^="acessorios[${aceId}]"]`).forEach(i => i.remove());
                    }
                    tr.remove();
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `<tr id="emptyAcessorioRow"><td colspan="4" class="text-center text-muted small py-4"><i class="fa-solid fa-puzzle-piece fs-4 d-block mb-2 text-secondary"></i>Nenhum componente associado.</td></tr>`;
                    }
                    atualizarCodigoNovoAcessorio();
                });

                designacaoInput.value = '';
                serieInput.value = '';

                atualizarCodigoNovoAcessorio();
            });
        }

        // LÓGICA INTELIGENTE DOS BOTÕES "PRÓXIMO" E "ANTERIOR"
        document.querySelectorAll('[data-bs-wizard-step]').forEach(button => {
            button.addEventListener('click', function(e) {

                const painelAtual = document.querySelector('.tab-pane.active');
                const textoBotao = this.innerText.toLowerCase();

                // Se for "Anterior", avança livremente
                if (textoBotao.includes("anterior")) {
                    mudarDeSeparador(this.getAttribute('data-bs-wizard-step'));
                    return;
                }

                let tudoValido = true;

                const alertaGlobal = painelAtual.querySelector('.alert-danger');
                if (alertaGlobal) alertaGlobal.classList.add('d-none');

                const feedbackSerial = document.getElementById('feedbackSerial');
                if (feedbackSerial) feedbackSerial.innerText = "Campo obrigatório.";
                const feedbackAno = document.getElementById('feedbackAno');
                if (feedbackAno) feedbackAno.innerText = "Data inválida.";
                const feedbackCusto = document.getElementById('feedbackCusto');
                if (feedbackCusto) feedbackCusto.innerText = "Campo obrigatório.";
                const erroDataGarantia = document.getElementById('erroDataGarantia');
                if (erroDataGarantia) erroDataGarantia.innerText = "Obrigatório.";
                const erroDataContrato = document.getElementById('erroDataContrato');
                if (erroDataContrato) erroDataContrato.innerText = "Obrigatório.";

                const camposObrigatorios = painelAtual.querySelectorAll('input[required], select[required]');
                camposObrigatorios.forEach(campo => {
                    if (campo.closest('.d-none')) return;

                    let elementoParaPintar = campo;
                    if (campo.type === 'hidden') {
                        elementoParaPintar = campo.nextElementSibling;
                    }

                    if (!campo.value || campo.value.trim() === '') {
                        elementoParaPintar.classList.add('is-invalid', 'border-danger');
                        tudoValido = false;
                    } else {
                        elementoParaPintar.classList.remove('is-invalid', 'border-danger');
                    }
                });

                const contGarantia = document.getElementById('camposGarantia');
                if (contGarantia && !contGarantia.classList.contains('d-none')) {
                    const dIniG = painelAtual.querySelector('input[name="garantiaInicio"]');
                    const dFimG = painelAtual.querySelector('input[name="garantiaFim"]');
                    if (dIniG && dFimG && dIniG.value && dFimG.value) {
                        if (new Date(dFimG.value) <= new Date(dIniG.value)) {
                            dFimG.classList.add('is-invalid', 'border-danger');
                            if (erroDataGarantia) erroDataGarantia.innerText = "Fim deve ser posterior ao início.";
                            tudoValido = false;
                        }
                    }
                }

                const contContrato = document.getElementById('camposContrato');
                if (contContrato && !contContrato.classList.contains('d-none')) {
                    const dIniC = painelAtual.querySelector('input[name="contratoInicio"]');
                    const dFimC = painelAtual.querySelector('input[name="contratoFim"]');
                    if (dIniC && dFimC && dIniC.value && dFimC.value) {
                        if (new Date(dFimC.value) <= new Date(dIniC.value)) {
                            dFimC.classList.add('is-invalid', 'border-danger');
                            if (erroDataContrato) erroDataContrato.innerText = "Fim deve ser posterior ao início.";
                            tudoValido = false;
                        }
                    }
                }

                if (painelAtual.id === 'step4-pane') {
                    const alertaPasso4 = document.getElementById('alertaPasso4');
                    const textoAlertaPasso4 = document.getElementById('textoAlertaPasso4');

                    const spansTipos = Array.from(painelAtual.querySelectorAll('.tipo-doc-anexado'));
                    const docsNaTabela = spansTipos.map(span => span.innerText);

                    let mensagemErroDocs = "";

                    const temContrato = document.getElementById('temContrato');
                    if (temContrato && temContrato.checked && !docsNaTabela.includes('Contrato de manutenção')) {
                        mensagemErroDocs += "Como indicou no Passo 3 que possui Contrato, tem de anexar o <strong>Contrato de manutenção</strong>.<br>";
                        tudoValido = false;
                    }

                    if (!document.getElementById('faltaCE').checked && !docsNaTabela.includes('Declaração de conformidade')) {
                        mensagemErroDocs += "Anexe a <strong>Declaração de conformidade</strong> ou assinale-a como em falta.<br>";
                        tudoValido = false;
                    }

                    if (!document.getElementById('faltaManual').checked && !docsNaTabela.includes('Manual de utilizador')) {
                        mensagemErroDocs += "Anexe o <strong>Manual de utilizador</strong> ou assinale-o como em falta.<br>";
                        tudoValido = false;
                    }

                    if (!document.getElementById('faltaFatura').checked && !docsNaTabela.includes('Fatura ou guia de aquisição')) {
                        mensagemErroDocs += "Anexe a <strong>Fatura ou guia de aquisição</strong> ou assinale-a como em falta.<br>";
                        tudoValido = false;
                    }

                    if (!tudoValido) {
                        textoAlertaPasso4.innerHTML = mensagemErroDocs;
                        alertaPasso4.classList.remove('d-none');
                    } else {
                        alertaPasso4.classList.add('d-none');
                    }
                }


                const nomeInput = painelAtual.querySelector('input[name="name"]');
                if (nomeInput && nomeInput.value.trim() !== '') {
                    if (nomeInput.value.trim().length < 3) {
                        nomeInput.classList.add('is-invalid', 'border-danger');
                        tudoValido = false;
                    }
                }

                const marcaInput = painelAtual.querySelector('input[name="brand"]');
                if (marcaInput && marcaInput.value.trim() !== '') {
                    if (/\d/.test(marcaInput.value.trim())) {
                        marcaInput.classList.add('is-invalid', 'border-danger');
                        marcaInput.nextElementSibling.innerText = "A marca não pode conter números.";
                        tudoValido = false;
                    }
                }

                const serialInput = painelAtual.querySelector('input[name="serialNumber"]');
                if (serialInput && serialInput.value.trim() !== '') {
                    if (!/^[a-zA-Z0-9\-_.]+$/.test(serialInput.value.trim())) {
                        serialInput.classList.add('is-invalid', 'border-danger');
                        if (feedbackSerial) feedbackSerial.innerText = "Caracteres inválidos. Use letras, números, hífen, ponto ou underscore.";
                        tudoValido = false;
                    }
                }

                const anoInput = painelAtual.querySelector('input[name="manufacturingYear"]');
                if (anoInput) {
                    if (!anoInput.value.trim()) {
                        anoInput.classList.add('is-invalid', 'border-danger');
                        if (feedbackAno) feedbackAno.innerText = "Campo obrigatório.";
                        tudoValido = false;
                    } else {
                        const ano = parseInt(anoInput.value);
                        const anoAtual = new Date().getFullYear();
                        if (ano < 1900 || ano > anoAtual) {
                            anoInput.classList.add('is-invalid', 'border-danger');
                            if (feedbackAno) feedbackAno.innerText = "Ano inválido. Deve ser entre 1900 e " + anoAtual + ".";
                            tudoValido = false;
                        } else {
                            anoInput.classList.remove('is-invalid', 'border-danger');
                        }
                    }
                }

                const dataAquisicaoInput = painelAtual.querySelector('input[name="acquisitionDate"]');
                if (dataAquisicaoInput) {
                    if (!dataAquisicaoInput.value) {
                        dataAquisicaoInput.classList.add('is-invalid', 'border-danger');
                        tudoValido = false;
                    } else {
                        const hoje = new Date().toISOString().split('T')[0]; // "2026-06-17"
                        if (dataAquisicaoInput.value > hoje) {
                            dataAquisicaoInput.classList.add('is-invalid', 'border-danger');
                            tudoValido = false;
                        } else {
                            dataAquisicaoInput.classList.remove('is-invalid', 'border-danger');
                        }
                    }
                }

                const custoInput = painelAtual.querySelector('input[name="cost"]');
                if (custoInput) {
                    if (!custoInput.value.trim()) {
                        custoInput.classList.add('is-invalid', 'border-danger');
                        if (feedbackCusto) feedbackCusto.innerText = "Campo obrigatório.";
                        tudoValido = false;
                    } else if (parseFloat(custoInput.value) < 0) {
                        custoInput.classList.add('is-invalid', 'border-danger');
                        if (feedbackCusto) feedbackCusto.innerText = "O valor não pode ser negativo.";
                        tudoValido = false;
                    }
                }

                if (tudoValido) {
                    if (painelAtual.id === 'step1-pane') {
                        const serialVal = painelAtual.querySelector('input[name="serialNumber"]')?.value.trim();
                        if (serialVal) {
                            const targetStep = this.getAttribute('data-bs-wizard-step');
                            fetch('api/verificar_serie.php?serie=' + encodeURIComponent(serialVal))
                                .then(res => res.json())
                                .then(data => {
                                    if (data.existe) {
                                        const sInput = painelAtual.querySelector('input[name="serialNumber"]');
                                        sInput.classList.add('is-invalid', 'border-danger');
                                        const fb = document.getElementById('feedbackSerial');
                                        if (fb) fb.innerText = "Este número de série já existe.";
                                        const alerta = painelAtual.querySelector('.alert-danger');
                                        if (alerta) alerta.classList.remove('d-none');
                                    } else {
                                        mudarDeSeparador(targetStep);
                                    }
                                })
                                .catch(() => {
                                    mudarDeSeparador(targetStep);
                                });
                        } else {
                            mudarDeSeparador(this.getAttribute('data-bs-wizard-step'));
                        }
                    } else {
                        mudarDeSeparador(this.getAttribute('data-bs-wizard-step'));
                    }
                } else {
                    if (alertaGlobal && painelAtual.id !== 'step4-pane') {
                        alertaGlobal.classList.remove('d-none');
                    }
                }
            });
        });

        function mudarDeSeparador(targetTabId) {
            const targetTabElement = document.querySelector(targetTabId);
            if (targetTabElement) {
                const tab = new bootstrap.Tab(targetTabElement);
                tab.show();
            }

            document.querySelectorAll('#equipamentoTabs .nav-link').forEach(navLink => {
                const badge = navLink.querySelector('.badge');
                const tabIdDoBotao = navLink.getAttribute('id');
                const tabAlvoCorrigido = targetTabId.replace('-pane', '-tab').replace('#', '');

                if (tabIdDoBotao === tabAlvoCorrigido) {
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

        const formEquipamento = document.querySelector('form');
        if (formEquipamento) {
            formEquipamento.addEventListener('submit', function(e) {
                const btnSubmit = document.getElementById('btnRegistarEquipamento');
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> A registar...';
                btnSubmit.disabled = true;
            });
        }
    });
</script>

<script>
    function selecionarDropdown(campoId, valorSelecionado) {
        const spanTexto = document.getElementById('text' + campoId.charAt(0).toUpperCase() + campoId.slice(1));
        spanTexto.innerText = valorSelecionado;
        spanTexto.classList.remove('text-muted');
        spanTexto.classList.add('text-dark');

        document.getElementById('input' + campoId.charAt(0).toUpperCase() + campoId.slice(1)).value = valorSelecionado;

        if (campoId === 'entryType') {
            const campoCusto = document.querySelector('input[name="cost"]');
            if (campoCusto) {
                if (valorSelecionado === 'Doação') {
                    campoCusto.value = '0';
                    campoCusto.readOnly = true;
                    campoCusto.classList.add('bg-light', 'text-muted');
                    campoCusto.title = "Doações têm custo zero.";
                } else {
                    campoCusto.readOnly = false;
                    campoCusto.classList.remove('bg-light', 'text-muted');
                    campoCusto.title = "";
                    if (campoCusto.value === '0') campoCusto.value = '';
                }
            }
        }
    }

    function filtrarDropdown(inputId, listaId) {
        let input = document.getElementById(inputId);
        let filter = input.value.toLowerCase();
        let container = document.getElementById(listaId);
        let items = container.getElementsByTagName('li');

        for (let i = 0; i < items.length; i++) {
            let texto = items[i].textContent || items[i].innerText;
            if (texto.toLowerCase().indexOf(filter) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }

    function selecionarLocalizacao(nivelAtual, valorSelecionado, proximoNivel) {
        const spanTexto = document.getElementById('text' + nivelAtual.charAt(0).toUpperCase() + nivelAtual.slice(1));
        spanTexto.innerText = valorSelecionado;
        spanTexto.classList.remove('text-muted');
        spanTexto.classList.add('text-dark');
        document.getElementById('input' + nivelAtual.charAt(0).toUpperCase() + nivelAtual.slice(1)).value = valorSelecionado;

        if (nivelAtual === 'edificio') {
            desativarNivelLocalizacao('piso', 'Aguardar edifício...');
            desativarNivelLocalizacao('servico', 'Aguardar piso...');
            desativarNivelLocalizacao('sala', 'Aguardar serviço...');
        } else if (nivelAtual === 'piso') {
            desativarNivelLocalizacao('servico', 'Aguardar piso...');
            desativarNivelLocalizacao('sala', 'Aguardar serviço...');
        } else if (nivelAtual === 'servico') {
            desativarNivelLocalizacao('sala', 'Aguardar serviço...');
        }

        if (proximoNivel) {
            const btnProximo = document.getElementById('btn' + proximoNivel.charAt(0).toUpperCase() + proximoNivel.slice(1));
            const listaProximo = document.getElementById('lista' + proximoNivel.charAt(0).toUpperCase() + proximoNivel.slice(1));

            btnProximo.classList.remove('disabled');

            let itens = [];

            if (proximoNivel === 'piso') {
                const ed = (window._dadosEdificios || []).find(e => e.nome === valorSelecionado);
                if (ed) {
                    itens = (window._dadosPisos || [])
                        .filter(p => p.id_edificio == ed.id_edificio)
                        .map(p => `<li data-id="${p.id_piso}" data-parent="${valorSelecionado}">
                        <a class="dropdown-item py-2" href="#"
                            onclick="selecionarLocalizacao('piso', '${p.designacao.replace(/'/g, "\\'")}', 'servico')">
                            ${p.designacao}</a></li>`);
                }
            } else if (proximoNivel === 'servico') {
                const edNome = document.getElementById('textEdificio').innerText;
                const ed = (window._dadosEdificios || []).find(e => e.nome === edNome);
                const piso = (window._dadosPisos || []).find(p => p.designacao === valorSelecionado && p.id_edificio == ed?.id_edificio);
                if (piso) {
                    itens = (window._dadosServicos || [])
                        .filter(s => s.id_piso == piso.id_piso)
                        .map(s => `<li data-id="${s.id_servico}" data-parent="${valorSelecionado}">
                        <a class="dropdown-item py-2" href="#"
                            onclick="selecionarLocalizacao('servico', '${s.nome.replace(/'/g, "\\'")}', 'sala')">
                            ${s.nome}</a></li>`);
                }
            } else if (proximoNivel === 'sala') {
                const pisoNome = document.getElementById('textPiso').innerText;
                const edNome = document.getElementById('textEdificio').innerText;
                const ed = (window._dadosEdificios || []).find(e => e.nome === edNome);
                const piso = (window._dadosPisos || []).find(p => p.designacao === pisoNome && p.id_edificio == ed?.id_edificio);
                const serv = (window._dadosServicos || []).find(s => s.nome === valorSelecionado && s.id_piso == piso?.id_piso);
                if (serv) {
                    const salasDeste = (window._dadosSalas || []).filter(sl => sl.id_servico == serv.id_servico);
                    if (salasDeste.length === 0) {
                        listaProximo.innerHTML = '<li class="px-3 py-2 text-muted small">Sem salas registadas</li>';
                    } else {
                        itens = salasDeste.map(sl => `<li data-id="${sl.id_sala}" data-parent="${valorSelecionado}">
                        <a class="dropdown-item py-2" href="#"
                            onclick="selecionarLocalizacao('sala', '${sl.identificacao.replace(/'/g, "\\'")}', null)">
                            ${sl.identificacao}</a></li>`);
                    }
                }
            }

            if (itens.length > 0) {
                listaProximo.innerHTML = itens.join('');
            }
        }
    }

    function desativarNivelLocalizacao(nivel, textoPlaceholder, bloquear = true) {
        const btn = document.getElementById('btn' + nivel.charAt(0).toUpperCase() + nivel.slice(1));
        const span = document.getElementById('text' + nivel.charAt(0).toUpperCase() + nivel.slice(1));

        if (bloquear) btn.classList.add('disabled');

        span.innerText = textoPlaceholder;
        span.classList.add('text-muted');
        span.classList.remove('text-dark');
        document.getElementById('input' + nivel.charAt(0).toUpperCase() + nivel.slice(1)).value = '';
    }

    function toggleCampos(switchId, containerId) {
        const checkBox = document.getElementById(switchId);
        const container = document.getElementById(containerId);

        if (checkBox.checked) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    async function carregarDadosFormulario() {
        try {
            const resposta = await fetch('api/get_dados_formulario.php');
            const dados = await resposta.json();

            if (!dados.sucesso) {
                console.error('Erro ao carregar dados do formulário:', dados.erro);
                return;
            }

            const listaManuf = document.getElementById('listaManufacturer');
            if (listaManuf) {
                listaManuf.innerHTML = dados.fornecedores.map(f =>
                    `<li><a class="dropdown-item py-1 small" href="#"
                    onclick="selecionarDropdown('manufacturer', '${f.nome_empresa.replace(/'/g, "\\'")}')">
                    ${f.nome_empresa}</a></li>`
                ).join('');
            }

            const listaForn = document.getElementById('listaFornecedor');
            if (listaForn) {
                listaForn.innerHTML = dados.fornecedores.map(f =>
                    `<li><a class="dropdown-item py-1 small" href="#"
                    onclick="selecionarDropdown('fornecedor', '${f.nome_empresa.replace(/'/g, "\\'")}')">
                    ${f.nome_empresa}</a></li>`
                ).join('');
            }

            const listaAss = document.getElementById('listaAssistencia');
            if (listaAss) {
                listaAss.innerHTML = dados.fornecedores.map(f =>
                    `<li><a class="dropdown-item py-1 small" href="#"
                    onclick="selecionarDropdown('assistencia', '${f.nome_empresa.replace(/'/g, "\\'")}')">
                    ${f.nome_empresa}</a></li>`
                ).join('');
            }

            const listaCons = document.getElementById('listaConsumiveis');
            if (listaCons) {
                listaCons.innerHTML = dados.fornecedores.map(f =>
                    `<li><a class="dropdown-item py-1 small" href="#"
                    onclick="selecionarDropdown('consumiveis', '${f.nome_empresa.replace(/'/g, "\\'")}')">
                    ${f.nome_empresa}</a></li>`
                ).join('');
            }

            const listaEnt = document.getElementById('listaEntidadeContrato');
            if (listaEnt) {
                listaEnt.innerHTML = dados.fornecedores.map(f =>
                    `<li><a class="dropdown-item py-1 small" href="#"
                    onclick="selecionarDropdown('entidadeContrato', '${f.nome_empresa.replace(/'/g, "\\'")}')">
                    ${f.nome_empresa}</a></li>`
                ).join('');
            }

            const listaDocForn = document.getElementById('listaDocFornecedor');
            if (listaDocForn) {
                listaDocForn.innerHTML =
                    `<li><a class="dropdown-item py-1 small" href="#"
            onclick="selecionarDropdown('docFornecedor', 'Nenhuma')">Nenhuma</a></li>` +
                    dados.fornecedores.map(f =>
                        `<li><a class="dropdown-item py-1 small" href="#"
                onclick="selecionarDropdown('docFornecedor', '${f.nome_empresa.replace(/'/g, "\\'")}')">
                ${f.nome_empresa}</a></li>`
                    ).join('');
            }

            const listaEd = document.getElementById('listaEdificio');
            if (listaEd) {
                listaEd.innerHTML = dados.edificios.map(e =>
                    `<li><a class="dropdown-item py-2" href="#"
                    onclick="selecionarLocalizacao('edificio', '${e.nome.replace(/'/g, "\\'")}', 'piso')"
                    data-id="${e.id_edificio}">
                    ${e.nome}</a></li>`
                ).join('');
            }

            window._dadosPisos = dados.pisos;
            window._dadosServicos = dados.servicos;
            window._dadosSalas = dados.salas;

            window._dadosEdificios = dados.edificios;

        } catch (erro) {
            console.error('Erro de comunicação ao carregar dados do formulário:', erro);
        }
    }

    carregarDadosFormulario();

    function verificarValidadeDoc(tipoSelecionado) {
        const campoValidade = document.getElementById('dataValidadeDoc');

        const tiposComValidade = [
            'Certificado de calibração'
        ];

        if (tiposComValidade.includes(tipoSelecionado)) {
            campoValidade.disabled = false;
            campoValidade.classList.remove('bg-light');
            campoValidade.classList.add('bg-white', 'border-warning'); // Destaca a amarelo
        } else {
            campoValidade.disabled = true;
            campoValidade.classList.add('bg-light');
            campoValidade.classList.remove('bg-white', 'border-warning');
            campoValidade.value = ''; // Limpa se for desativado
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        <?php if (!empty($_POST['manufacturer'])): ?>
            selecionarDropdown('manufacturer', <?= json_encode($_POST['manufacturer']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['categoria'])): ?>
            selecionarDropdown('categoria', <?= json_encode($_POST['categoria']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['criticidade'])): ?>
            selecionarDropdown('criticidade', <?= json_encode($_POST['criticidade']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['entryType'])): ?>
            selecionarDropdown('entryType', <?= json_encode($_POST['entryType']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['status'])): ?>
            selecionarDropdown('status', <?= json_encode($_POST['status']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['edificio'])): ?>
            selecionarLocalizacao('edificio', <?= json_encode($_POST['edificio']) ?>, 'piso');
        <?php endif; ?>

        <?php if (!empty($_POST['piso'])): ?>
            selecionarLocalizacao('piso', <?= json_encode($_POST['piso']) ?>, 'servico');
        <?php endif; ?>

        <?php if (!empty($_POST['servico'])): ?>
            selecionarLocalizacao('servico', <?= json_encode($_POST['servico']) ?>, 'sala');
        <?php endif; ?>

        <?php if (!empty($_POST['sala'])): ?>
            selecionarLocalizacao('sala', <?= json_encode($_POST['sala']) ?>, null);
        <?php endif; ?>

        <?php if (!empty($_POST['fornecedor'])): ?>
            selecionarDropdown('fornecedor', <?= json_encode($_POST['fornecedor']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['assistencia'])): ?>
            selecionarDropdown('assistencia', <?= json_encode($_POST['assistencia']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['consumiveis'])): ?>
            selecionarDropdown('consumiveis', <?= json_encode($_POST['consumiveis']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['entidadeContrato'])): ?>
            selecionarDropdown('entidadeContrato', <?= json_encode($_POST['entidadeContrato']) ?>);
        <?php endif; ?>

        <?php if (!empty($_POST['garantiaInicio'])): ?>
            document.querySelector('input[name="garantiaInicio"]').value = <?= json_encode($_POST['garantiaInicio']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['garantiaFim'])): ?>
            document.querySelector('input[name="garantiaFim"]').value = <?= json_encode($_POST['garantiaFim']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['referenciaContrato'])): ?>
            document.querySelector('input[name="referenciaContrato"]').value = <?= json_encode($_POST['referenciaContrato']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['tipoContrato'])): ?>
            document.querySelector('select[name="tipoContrato"]').value = <?= json_encode($_POST['tipoContrato']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['periodicidadeContrato'])): ?>
            document.querySelector('select[name="periodicidadeContrato"]').value = <?= json_encode($_POST['periodicidadeContrato']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['contratoInicio'])): ?>
            document.querySelector('input[name="contratoInicio"]').value = <?= json_encode($_POST['contratoInicio']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['contratoFim'])): ?>
            document.querySelector('input[name="contratoFim"]').value = <?= json_encode($_POST['contratoFim']) ?>;
        <?php endif; ?>

        <?php if (!empty($_POST['observations'])): ?>
            document.querySelector('textarea[name="observations"]').value = <?= json_encode($_POST['observations']) ?>;
        <?php endif; ?>
    });
</script>

<script>
document.getElementById('btn-preencher-auto').addEventListener('click', function() {

    document.querySelector('input[name="name"]').value           = 'Desfibrilhador Automático Externo';
    document.querySelector('input[name="brand"]').value          = 'Philips';
    document.querySelector('input[name="model"]').value          = 'HeartStart FRx';

    const anoAtual  = new Date().getFullYear();
    const aleatorio = Math.floor(10000 + Math.random() * 90000);
    document.querySelector('input[name="serialNumber"]').value   = `DAE-${anoAtual}-PH-${aleatorio}`;

    document.querySelector('input[name="manufacturingYear"]').value = '2024';

    selecionarDropdown('categoria', 'Suporte de Vida');

    selecionarDropdown('criticidade', 'Suporte de Vida');

    document.querySelector('input[name="acquisitionDate"]').value = '2024-03-15';
    document.querySelector('input[name="cost"]').value            = '2850.00';

    selecionarDropdown('entryType', 'Compra');
    selecionarDropdown('status',    'Ativo');

    const chkGarantia = document.getElementById('temGarantia');
    if (chkGarantia && !chkGarantia.checked) chkGarantia.click();
    setTimeout(() => {
        const gIni = document.querySelector('input[name="garantiaInicio"]');
        const gFim = document.querySelector('input[name="garantiaFim"]');
        if (gIni) gIni.value = '2024-03-15';
        if (gFim) gFim.value = '2027-03-15';
    }, 300);

    const obsField = document.querySelector('textarea[name="observations"]');
    if (obsField) obsField.value = 'Equipamento adquirido no âmbito do reforço do inventário de suporte de vida. Instalado na UCI com formação da equipa realizada em Abril de 2024.';

    const btn = document.getElementById('btn-preencher-auto');
    const textoOriginal = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Preenchido!</span>';
    btn.classList.replace('btn-outline-secondary', 'btn-success');
    setTimeout(() => {
        btn.innerHTML = textoOriginal;
        btn.classList.replace('btn-success', 'btn-outline-secondary');
    }, 2500);
});
</script>

<?php include '../../includes/footer.php'; ?>