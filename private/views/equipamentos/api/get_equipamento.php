<?php
require_once __DIR__ . '/../../../includes/funcoes.php';
require_once __DIR__ . '/../../../../config/config.php';

redirect_if_not_logged();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID não fornecido.']);
    exit;
}

$id_equipamento = aes_decrypt($_GET['id'] ?? '');
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

    // --- Dados principais do equipamento + localização + fabricante ---
    $stmt = $ligacao->prepare("
        SELECT
            e.*,
            s.nome        AS nome_servico,
            sa.identificacao AS nome_sala,
            p.designacao  AS nome_piso,
            ed.nome       AS nome_edificio,
            f.nome_empresa AS nome_fabricante
        FROM equipamentos e
        LEFT JOIN servicos  s   ON s.id_servico  = e.id_servico
        LEFT JOIN salas     sa  ON sa.id_sala     = e.id_sala
        LEFT JOIN pisos     p   ON p.id_piso      = s.id_piso
        LEFT JOIN edificios ed  ON ed.id_edificio = p.id_edificio
        LEFT JOIN fornecedores f ON f.id_fornecedor = e.id_fabricante
        WHERE e.id_equipamento = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id_equipamento]);
    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        echo json_encode(['sucesso' => false, 'erro' => 'Equipamento não encontrado.']);
        exit;
    }

    // --- Fornecedores associados (tabela equipamento_fornecedor) ---
    $stmtForn = $ligacao->prepare("
        SELECT
            f.id_fornecedor,
            f.nome_empresa,
            f.telefone_geral,
            f.email_geral,
            ef.papel
        FROM equipamento_fornecedor ef
        INNER JOIN fornecedores f ON f.id_fornecedor = ef.id_fornecedor
        WHERE ef.id_equipamento = :id
        ORDER BY ef.papel ASC
    ");
    $stmtForn->execute([':id' => $id_equipamento]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    // --- Garantias e contratos ---
    $stmtGar = $ligacao->prepare("
        SELECT
            gc.id_contrato,
            gc.tipo_cobertura,
            gc.referencia,
            gc.tipo_contrato,
            gc.periodicidade,
            gc.data_inicio,
            gc.data_fim,
            f.nome_empresa AS entidade_responsavel
        FROM garantias_contratos gc
        LEFT JOIN fornecedores f ON f.id_fornecedor = gc.id_entidade_responsavel
        WHERE gc.id_equipamento = :id
        ORDER BY gc.data_fim ASC
    ");
    $stmtGar->execute([':id' => $id_equipamento]);
    $garantias = $stmtGar->fetchAll(PDO::FETCH_ASSOC);

    // --- Documentos ---
    $stmtDoc = $ligacao->prepare("
        SELECT
            d.id_documento,
            d.tipo_documento,
            d.titulo,
            d.caminho_ficheiro,
            d.data_emissao,
            d.data_validade
        FROM documentos d
        WHERE d.id_equipamento = :id
        ORDER BY d.data_upload DESC
    ");
    $stmtDoc->execute([':id' => $id_equipamento]);
    $documentos = $stmtDoc->fetchAll(PDO::FETCH_ASSOC);

    // --- Acessórios ---
    $stmtAce = $ligacao->prepare("
        SELECT
            a.id_acessorio,
            a.codigo_componente,
            a.designacao,
            a.numero_serie
        FROM acessorios a
        WHERE a.id_equipamento = :id
        ORDER BY a.codigo_componente ASC
    ");
    $stmtAce->execute([':id' => $id_equipamento]);
    $acessorios = $stmtAce->fetchAll(PDO::FETCH_ASSOC);

    // --- Histórico de movimentações ---
    $stmtHist = $ligacao->prepare("
        SELECT
            hm.data_movimento,
            hm.motivo,
            so.nome AS servico_origem,
            sd.nome AS servico_destino,
            u.nome  AS utilizador
        FROM historico_movimentacoes hm
        LEFT JOIN servicos so ON so.id_servico = hm.id_servico_origem
        LEFT JOIN servicos sd ON sd.id_servico = hm.id_servico_destino
        LEFT JOIN utilizadores u ON u.id_utilizador = hm.id_utilizador
        WHERE hm.id_equipamento = :id
        ORDER BY hm.data_movimento DESC
    ");
    $stmtHist->execute([':id' => $id_equipamento]);
    $historico = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso'      => true,
        'dados'        => $equipamento,
        'fornecedores' => $fornecedores,
        'garantias'    => $garantias,
        'documentos'   => $documentos,
        'acessorios'   => $acessorios,
        'historico'    => $historico
    ]);

} catch (PDOException $err) {
    echo json_encode([
        'sucesso' => false,
        'erro'    => 'Erro na BD: ' . $err->getMessage()
    ]);
}

$ligacao = null;