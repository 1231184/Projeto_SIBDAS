<?php
// API: Exportar ficha de equipamento em CSV
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
        SELECT e.*, s.nome AS nome_servico, sa.identificacao AS nome_sala,
               p.designacao AS nome_piso, ed.nome AS nome_edificio,
               f.nome_empresa AS nome_fabricante
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
    $stmtGar = $ligacao->prepare("SELECT tipo_cobertura, referencia, data_inicio, data_fim FROM garantias_contratos WHERE id_equipamento = :id ORDER BY data_fim ASC");
    $stmtGar->execute([':id' => $id_equipamento]);
    $garantias = $stmtGar->fetchAll(PDO::FETCH_ASSOC);

    // Fornecedores
    $stmtForn = $ligacao->prepare("SELECT f.nome_empresa, f.telefone_geral, f.email_geral, ef.papel FROM equipamento_fornecedor ef INNER JOIN fornecedores f ON f.id_fornecedor = ef.id_fornecedor WHERE ef.id_equipamento = :id");
    $stmtForn->execute([':id' => $id_equipamento]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;

} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro na base de dados.');
}

// Gerar CSV
$nome_ficheiro = 'equipamento_' . preg_replace('/[^a-z0-9]/i', '_', $eq['codigo_interno']) . '_' . date('Ymd') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nome_ficheiro . '"');

$out = fopen('php://output', 'w');

// BOM UTF-8 para Excel abrir corretamente
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

// --- DADOS GERAIS ---
fputcsv($out, ['FICHA TÉCNICA DO EQUIPAMENTO'], ';');
fputcsv($out, ['Gerado em:', date('d/m/Y H:i')], ';');
fputcsv($out, [], ';');

fputcsv($out, ['IDENTIFICAÇÃO'], ';');
fputcsv($out, ['Código Interno', $eq['codigo_interno']], ';');
fputcsv($out, ['Designação', $eq['designacao']], ';');
fputcsv($out, ['Marca', $eq['marca']], ';');
fputcsv($out, ['Modelo', $eq['modelo']], ';');
fputcsv($out, ['Número de Série', $eq['numero_serie']], ';');
fputcsv($out, ['Ano de Fabrico', $eq['ano_fabrico'] ?? '—'], ';');
fputcsv($out, ['Categoria', $eq['categoria']], ';');
fputcsv($out, ['Criticidade', $eq['criticidade']], ';');
fputcsv($out, ['Estado', $eq['estado']], ';');
fputcsv($out, ['Fabricante', $eq['nome_fabricante'] ?? '—'], ';');
fputcsv($out, [], ';');

fputcsv($out, ['AQUISIÇÃO'], ';');
fputcsv($out, ['Tipo de Entrada', $eq['tipo_entrada']], ';');
fputcsv($out, ['Data de Aquisição', $eq['data_aquisicao'] ? date('d/m/Y', strtotime($eq['data_aquisicao'])) : '—'], ';');
fputcsv($out, ['Custo de Aquisição', $eq['custo_aquisicao'] ? number_format($eq['custo_aquisicao'], 2, ',', '.') . ' €' : '—'], ';');
fputcsv($out, [], ';');

fputcsv($out, ['LOCALIZAÇÃO'], ';');
fputcsv($out, ['Edifício', $eq['nome_edificio'] ?? '—'], ';');
fputcsv($out, ['Piso', $eq['nome_piso'] ?? '—'], ';');
fputcsv($out, ['Serviço', $eq['nome_servico'] ?? '—'], ';');
fputcsv($out, ['Sala', $eq['nome_sala'] ?? '—'], ';');
fputcsv($out, [], ';');

// --- ACESSÓRIOS ---
if (!empty($acessorios)) {
    fputcsv($out, ['ACESSÓRIOS'], ';');
    fputcsv($out, ['Código', 'Designação', 'Nº Série'], ';');
    foreach ($acessorios as $a) {
        fputcsv($out, [$a['codigo_componente'] ?? '—', $a['designacao'], $a['numero_serie'] ?? '—'], ';');
    }
    fputcsv($out, [], ';');
}

// --- FORNECEDORES ---
if (!empty($fornecedores)) {
    fputcsv($out, ['FORNECEDORES'], ';');
    fputcsv($out, ['Empresa', 'Papel', 'Telefone', 'Email'], ';');
    foreach ($fornecedores as $f) {
        fputcsv($out, [$f['nome_empresa'], $f['papel'], $f['telefone_geral'], $f['email_geral']], ';');
    }
    fputcsv($out, [], ';');
}

// --- GARANTIAS ---
if (!empty($garantias)) {
    fputcsv($out, ['GARANTIAS E CONTRATOS'], ';');
    fputcsv($out, ['Tipo', 'Referência', 'Início', 'Fim'], ';');
    foreach ($garantias as $g) {
        fputcsv($out, [
            $g['tipo_cobertura'],
            $g['referencia'] ?? '—',
            $g['data_inicio'] ? date('d/m/Y', strtotime($g['data_inicio'])) : '—',
            $g['data_fim'] ? date('d/m/Y', strtotime($g['data_fim'])) : '—'
        ], ';');
    }
    fputcsv($out, [], ';');
}

// --- OBSERVAÇÕES ---
if (!empty($eq['observacoes'])) {
    fputcsv($out, ['OBSERVAÇÕES'], ';');
    fputcsv($out, [$eq['observacoes']], ';');
}

fclose($out);