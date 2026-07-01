<?php
// API: Exportar ficha de equipamento em JSON
require_once __DIR__ . '/../../../includes/funcoes.php';
require_once __DIR__ . '/../../../../config/config.php';

redirect_if_not_logged();

$id_equipamento = aes_decrypt($_GET['id'] ?? '');
if (!$id_equipamento || !is_numeric($id_equipamento)) {
    http_response_code(400);
    exit('ID inválido.');
}
$id_equipamento = (int)$id_equipamento;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Dados principais
    $stmt = $ligacao->prepare("
        SELECT e.codigo_interno, e.designacao, e.marca, e.modelo, e.numero_serie,
               e.ano_fabrico, e.categoria, e.criticidade, e.estado,
               e.tipo_entrada, e.data_aquisicao, e.custo_aquisicao,
               e.falta_declaracao_ce, e.falta_manual_utilizador, e.falta_fatura_guia,
               e.observacoes, e.data_registo,
               s.nome AS servico, sa.identificacao AS sala,
               p.designacao AS piso, ed.nome AS edificio,
               f.nome_empresa AS fabricante
        FROM equipamentos e
        LEFT JOIN servicos  s   ON s.id_servico  = e.id_servico
        LEFT JOIN salas     sa  ON sa.id_sala     = e.id_sala
        LEFT JOIN pisos     p   ON p.id_piso      = s.id_piso
        LEFT JOIN edificios ed  ON ed.id_edificio = p.id_edificio
        LEFT JOIN fornecedores f ON f.id_fornecedor = e.id_fabricante
        WHERE e.id_equipamento = :id LIMIT 1
    ");
    $stmt->execute([':id' => $id_equipamento]);
    $eq = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$eq) { http_response_code(404); exit('Equipamento não encontrado.'); }

    // Acessórios
    $stmtAce = $ligacao->prepare("SELECT codigo_componente, designacao, numero_serie FROM acessorios WHERE id_equipamento = :id ORDER BY codigo_componente ASC");
    $stmtAce->execute([':id' => $id_equipamento]);
    $acessorios = $stmtAce->fetchAll(PDO::FETCH_ASSOC);

    // Garantias
    $stmtGar = $ligacao->prepare("
        SELECT gc.tipo_cobertura, gc.referencia, gc.tipo_contrato, gc.periodicidade,
               gc.data_inicio, gc.data_fim, f.nome_empresa AS entidade_responsavel
        FROM garantias_contratos gc
        LEFT JOIN fornecedores f ON f.id_fornecedor = gc.id_entidade_responsavel
        WHERE gc.id_equipamento = :id ORDER BY gc.data_fim ASC
    ");
    $stmtGar->execute([':id' => $id_equipamento]);
    $garantias = $stmtGar->fetchAll(PDO::FETCH_ASSOC);

    // Fornecedores
    $stmtForn = $ligacao->prepare("
        SELECT f.nome_empresa, f.telefone_geral, f.email_geral, ef.papel
        FROM equipamento_fornecedor ef
        INNER JOIN fornecedores f ON f.id_fornecedor = ef.id_fornecedor
        WHERE ef.id_equipamento = :id
    ");
    $stmtForn->execute([':id' => $id_equipamento]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    // Documentos
    $stmtDoc = $ligacao->prepare("SELECT tipo_documento, titulo, data_emissao, data_validade FROM documentos WHERE id_equipamento = :id ORDER BY data_upload DESC");
    $stmtDoc->execute([':id' => $id_equipamento]);
    $documentos = $stmtDoc->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;

} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro na base de dados.');
}

// Estrutura JSON
$ficha = [
    'meta' => [
        'sistema'     => 'MedStock Solutions',
        'exportado_em' => date('Y-m-d H:i:s'),
        'exportado_por' => $_SESSION['nome'] ?? $_SESSION['utilizador'] ?? 'Sistema'
    ],
    'equipamento' => $eq,
    'acessorios'  => $acessorios,
    'fornecedores' => $fornecedores,
    'garantias'   => $garantias,
    'documentos'  => $documentos
];

$nome_ficheiro = 'equipamento_' . preg_replace('/[^a-z0-9]/i', '_', $eq['codigo_interno']) . '_' . date('Ymd') . '.json';

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nome_ficheiro . '"');

echo json_encode($ficha, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);