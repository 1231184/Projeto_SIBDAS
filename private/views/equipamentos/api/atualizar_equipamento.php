<?php
require_once __DIR__ . '/../../../includes/funcoes.php';
require_once __DIR__ . '/../../../../config/config.php';

redirect_if_not_logged();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido.']);
    exit;
}

$id_equipamento = aes_decrypt($_POST['id_equipamento'] ?? '');
if (!$id_equipamento || !is_numeric($id_equipamento)) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID inválido ou manipulado.']);
    exit;
}
$id_equipamento = (int)$id_equipamento;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $ligacao->beginTransaction();


    $nome_servico  = trim($_POST['servico']      ?? '');
    $nome_sala     = trim($_POST['sala']         ?? '');
    $nome_fabricante = trim($_POST['manufacturer'] ?? '');

    $id_servico    = null;
    $id_sala       = null;
    $id_fabricante = null;

    if (!empty($nome_servico)) {
        $stmt = $ligacao->prepare("SELECT id_servico FROM servicos WHERE nome = :nome LIMIT 1");
        $stmt->execute([':nome' => $nome_servico]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row) $id_servico = $row->id_servico;
    }

    if (!empty($nome_sala) && $id_servico) {
        $stmt = $ligacao->prepare("SELECT id_sala FROM salas WHERE identificacao = :ident AND id_servico = :id_serv LIMIT 1");
        $stmt->execute([':ident' => $nome_sala, ':id_serv' => $id_servico]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row) $id_sala = $row->id_sala;
    }

    if (!empty($nome_fabricante)) {
        $stmt = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
        $stmt->execute([':nome' => $nome_fabricante]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row) $id_fabricante = $row->id_fornecedor;
    }


    $designacao   = trim($_POST['name']              ?? '');
    $marca        = trim($_POST['brand']             ?? '');
    $modelo       = trim($_POST['model']             ?? '');
    $numero_serie = trim($_POST['serialNumber']      ?? '');
    $ano_fabrico  = trim($_POST['manufacturingYear'] ?? '');
    $categoria    = trim($_POST['categoria']         ?? '');
    $criticidade  = trim($_POST['criticidade']       ?? '');
    $data_aquisicao = trim($_POST['acquisitionDate'] ?? '');
    $custo        = trim($_POST['cost']              ?? '');
    $tipo_entrada = trim($_POST['entryType']         ?? '');
    $estado       = trim($_POST['status']            ?? '');
    $observacoes  = trim($_POST['observations']      ?? '');

    $falta_ce     = isset($_POST['faltaCE'])     ? 1 : 0;
    $falta_manual = isset($_POST['faltaManual']) ? 1 : 0;
    $falta_fatura = isset($_POST['faltaFatura']) ? 1 : 0;

    $stmt = $ligacao->prepare("
        UPDATE equipamentos SET
            designacao              = :designacao,
            marca                   = :marca,
            modelo                  = :modelo,
            numero_serie            = :numero_serie,
            ano_fabrico             = :ano_fabrico,
            categoria               = :categoria,
            criticidade             = :criticidade,
            data_aquisicao          = :data_aquisicao,
            custo_aquisicao         = :custo,
            tipo_entrada            = :tipo_entrada,
            estado                  = :estado,
            falta_declaracao_ce     = :falta_ce,
            falta_manual_utilizador = :falta_manual,
            falta_fatura_guia       = :falta_fatura,
            observacoes             = :observacoes,
            id_servico              = :id_servico,
            id_sala                 = :id_sala,
            id_fabricante           = :id_fabricante
        WHERE id_equipamento = :id
    ");
    $stmt->execute([
        ':designacao'    => $designacao,
        ':marca'         => $marca,
        ':modelo'        => strtoupper($modelo),
        ':numero_serie'  => $numero_serie,
        ':ano_fabrico'   => !empty($ano_fabrico)    ? (int)$ano_fabrico    : null,
        ':categoria'     => $categoria,
        ':criticidade'   => $criticidade,
        ':data_aquisicao' => !empty($data_aquisicao) ? $data_aquisicao      : null,
        ':custo'         => ($custo !== '')          ? (float)$custo        : null,
        ':tipo_entrada'  => $tipo_entrada,
        ':estado'        => $estado,
        ':falta_ce'      => $falta_ce,
        ':falta_manual'  => $falta_manual,
        ':falta_fatura'  => $falta_fatura,
        ':observacoes'   => $observacoes ?: null,
        ':id_servico'    => $id_servico,
        ':id_sala'       => $id_sala,
        ':id_fabricante' => $id_fabricante,
        ':id'            => $id_equipamento,
    ]);


    $ligacao->prepare("DELETE FROM equipamento_fornecedor WHERE id_equipamento = :id")
        ->execute([':id' => $id_equipamento]);

    $papeisParaInserir = [];
    $nome_fornecedor  = trim($_POST['fornecedor']  ?? '');
    $nome_assistencia = trim($_POST['assistencia'] ?? '');
    $nome_consumiveis = trim($_POST['consumiveis'] ?? '');

    if (!empty($nome_fornecedor))  $papeisParaInserir[] = ['nome' => $nome_fornecedor,  'papel' => 'Comercial'];
    if (!empty($nome_assistencia)) $papeisParaInserir[] = ['nome' => $nome_assistencia, 'papel' => 'Assistência'];
    if (!empty($nome_consumiveis)) $papeisParaInserir[] = ['nome' => $nome_consumiveis, 'papel' => 'Consumíveis'];

    foreach ($papeisParaInserir as $entry) {
        $stmtF = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
        $stmtF->execute([':nome' => $entry['nome']]);
        $rowF = $stmtF->fetch(PDO::FETCH_OBJ);
        if ($rowF) {
            $ligacao->prepare("INSERT IGNORE INTO equipamento_fornecedor (id_equipamento, id_fornecedor, papel) VALUES (:id_eq, :id_f, :papel)")
                ->execute([':id_eq' => $id_equipamento, ':id_f' => $rowF->id_fornecedor, ':papel' => $entry['papel']]);
        }
    }


    $ligacao->prepare("DELETE FROM garantias_contratos WHERE id_equipamento = :id")
        ->execute([':id' => $id_equipamento]);

    $tem_garantia = isset($_POST['temGarantia']);
    $tem_contrato = isset($_POST['temContrato']);

    if ($tem_garantia) {
        $g_inicio = trim($_POST['garantiaInicio'] ?? '');
        $g_fim    = trim($_POST['garantiaFim']    ?? '');
        if (!empty($g_inicio) && !empty($g_fim)) {
            $ligacao->prepare("
                INSERT INTO garantias_contratos (id_equipamento, tipo_cobertura, data_inicio, data_fim)
                VALUES (:id_eq, 'Garantia', :inicio, :fim)
            ")->execute([':id_eq' => $id_equipamento, ':inicio' => $g_inicio, ':fim' => $g_fim]);
        }
    }

    if ($tem_contrato) {
        $c_ref    = trim($_POST['referenciaContrato']    ?? '');
        $c_ent    = trim($_POST['entidadeContrato']      ?? '');
        $c_tipo   = trim($_POST['tipoContrato']          ?? '');
        $c_period = trim($_POST['periodicidadeContrato'] ?? '');
        $c_inicio = trim($_POST['contratoInicio']        ?? '');
        $c_fim    = trim($_POST['contratoFim']           ?? '');

        $id_entidade = null;
        if (!empty($c_ent)) {
            $stmtEnt = $ligacao->prepare("SELECT id_fornecedor FROM fornecedores WHERE nome_empresa = :nome LIMIT 1");
            $stmtEnt->execute([':nome' => $c_ent]);
            $rowEnt = $stmtEnt->fetch(PDO::FETCH_OBJ);
            if ($rowEnt) $id_entidade = $rowEnt->id_fornecedor;
        }

        if (!empty($c_inicio) && !empty($c_fim)) {
            $ligacao->prepare("
                INSERT INTO garantias_contratos
                    (id_equipamento, tipo_cobertura, referencia, id_entidade_responsavel, tipo_contrato, periodicidade, data_inicio, data_fim)
                VALUES
                    (:id_eq, 'Contrato Manutenção', :ref, :id_ent, :tipo, :period, :inicio, :fim)
            ")->execute([
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


    $ligacao->prepare("DELETE FROM acessorios WHERE id_equipamento = :id")
        ->execute([':id' => $id_equipamento]);

    $acessorios_post = $_POST['acessorios'] ?? [];
    foreach ($acessorios_post as $ace) {
        $ace_codigo     = trim($ace['codigo']     ?? '');
        $ace_designacao = trim($ace['designacao'] ?? '');
        $ace_serie      = trim($ace['serie']      ?? '');
        if (empty($ace_designacao)) continue;

        $ligacao->prepare("
            INSERT INTO acessorios (id_equipamento, codigo_componente, designacao, numero_serie)
            VALUES (:id_eq, :cod, :des, :ser)
        ")->execute([
            ':id_eq' => $id_equipamento,
            ':cod'   => $ace_codigo     ?: null,
            ':des'   => $ace_designacao,
            ':ser'   => $ace_serie      ?: null,
        ]);
    }


    $docs_remover = $_POST['ids_docs_remover'] ?? [];
    foreach ($docs_remover as $id_doc) {
        $id_doc = (int)$id_doc;
        if ($id_doc) {
            $ligacao->prepare("DELETE FROM documentos WHERE id_documento = :id AND id_equipamento = :id_eq")
                ->execute([':id' => $id_doc, ':id_eq' => $id_equipamento]);
        }
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

        $caminho_ficheiro = 'assets/docs/sem_ficheiro.pdf';
        if (isset($_FILES['docFicheiros']['tmp_name'][$doc_index]) && $_FILES['docFicheiros']['error'][$doc_index] === UPLOAD_ERR_OK) {
            $pasta_tipo   = $pastas[$doc_tipo] ?? 'outros';
            $pasta_dest   = __DIR__ . '/../../../../assets/docs/' . $pasta_tipo . '/';

            if (!is_dir($pasta_dest)) mkdir($pasta_dest, 0755, true);

            $nome_original = basename($_FILES['docFicheiros']['name'][$doc_index]);
            $extensao      = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
            $codigo_interno = $_POST['internalCode'] ?? 'EQ-0000';
            $nome_seguro   = $codigo_interno . '_' . $pasta_tipo . '_edit_' . time() . '_' . ($doc_index + 1) . '.' . $extensao;
            $caminho_full  = $pasta_dest . $nome_seguro;

            if (move_uploaded_file($_FILES['docFicheiros']['tmp_name'][$doc_index], $caminho_full)) {
                $caminho_ficheiro = 'assets/docs/' . $pasta_tipo . '/' . $nome_seguro;
            }
        }

        $ligacao->prepare("
            INSERT INTO documentos (id_equipamento, id_fornecedor, tipo_documento, titulo, data_emissao, data_validade, caminho_ficheiro)
            VALUES (:id_eq, :id_forn, :tipo, :titulo, :emissao, :validade, :caminho)
        ")->execute([
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


    $id_utilizador = $_SESSION['id_utilizador'] ?? null;
    if ($id_servico) {
       
        $stmtServ = $ligacao->prepare("SELECT id_servico FROM equipamentos WHERE id_equipamento = :id");
        
        $ligacao->prepare("
            INSERT INTO historico_movimentacoes (id_equipamento, id_servico_origem, id_servico_destino, motivo, id_utilizador)
            VALUES (:id_eq, NULL, :id_serv, 'Atualização de registo', :id_util)
        ")->execute([
            ':id_eq'   => $id_equipamento,
            ':id_serv' => $id_servico,
            ':id_util' => $id_utilizador,
        ]);
    }

    $ligacao->commit();
    $ligacao = null;

    echo json_encode(['sucesso' => true, 'mensagem' => 'Equipamento atualizado com sucesso.']);
} catch (PDOException $err) {
    if (isset($ligacao)) $ligacao->rollBack();
    $ligacao = null;

    
    if ($err->getCode() == 23000 && strpos($err->getMessage(), 'numero_serie') !== false) {
        echo json_encode(['sucesso' => false, 'erro' => 'Este número de série já existe noutro equipamento.']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $err->getMessage()]);
    }
}
