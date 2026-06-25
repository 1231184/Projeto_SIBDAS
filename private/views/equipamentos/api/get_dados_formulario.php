<?php
require_once __DIR__ . '/../../../includes/funcoes.php';
require_once __DIR__ . '/../../../../config/config.php';

redirect_if_not_logged();

header('Content-Type: application/json; charset=utf-8');

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- FORNECEDORES (todos, ordenados por nome) ---
    $fornecedores = $ligacao->query("
        SELECT id_fornecedor, nome_empresa, tipo_fornecedor
        FROM fornecedores
        ORDER BY nome_empresa ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // --- EDIFÍCIOS ---
    $edificios = $ligacao->query("
        SELECT id_edificio, nome
        FROM edificios
        ORDER BY nome ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // --- PISOS (com id do edifício pai para filtrar no frontend) ---
    $pisos = $ligacao->query("
        SELECT id_piso, id_edificio, designacao
        FROM pisos
        ORDER BY id_edificio ASC, designacao ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // --- SERVIÇOS (com id do piso pai para filtrar no frontend) ---
    $servicos = $ligacao->query("
        SELECT id_servico, id_piso, nome
        FROM servicos
        ORDER BY id_piso ASC, nome ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // --- SALAS (com id do serviço pai para filtrar no frontend) ---
    $salas = $ligacao->query("
        SELECT id_sala, id_servico, identificacao
        FROM salas
        ORDER BY id_servico ASC, identificacao ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;

    echo json_encode([
        'sucesso'     => true,
        'fornecedores' => $fornecedores,
        'edificios'   => $edificios,
        'pisos'       => $pisos,
        'servicos'    => $servicos,
        'salas'       => $salas
    ]);

} catch (PDOException $err) {
    echo json_encode([
        'sucesso' => false,
        'erro'    => 'Erro na BD: ' . $err->getMessage()
    ]);
}