<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../includes/funcoes.php';

header('Content-Type: application/json');
redirect_if_not_logged();

if (!isset($_GET['id_servico']) || !is_numeric($_GET['id_servico'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID de serviço inválido.']);
    exit;
}

$id_servico = (int)$_GET['id_servico'];

try {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Para cada sala, conta equipamentos
    $sql = "
        SELECT
            sl.id_sala,
            sl.id_servico,
            sl.identificacao,
            sl.observacoes,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_sala = sl.id_sala
            ) AS total_equipamentos
        FROM salas sl
        WHERE sl.id_servico = :id_servico
        ORDER BY sl.identificacao ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_servico', $id_servico, PDO::PARAM_INT);
    $stmt->execute();
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar também estatísticas globais do serviço para o painel de resumo
    $sqlServico = "
        SELECT
            s.nome,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico
            ) AS total_equipamentos,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico AND eq.estado = 'Ativo'
            ) AS total_ativos,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico AND eq.estado = 'Em Manutenção'
            ) AS total_manutencao,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico AND eq.criticidade = 'Suporte de Vida'
            ) AS total_criticos
        FROM servicos s
        WHERE s.id_servico = :id_servico
        LIMIT 1
    ";

    $stmtS = $pdo->prepare($sqlServico);
    $stmtS->bindParam(':id_servico', $id_servico, PDO::PARAM_INT);
    $stmtS->execute();
    $servico = $stmtS->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso'  => true,
        'salas'    => $salas,
        'servico'  => $servico
    ]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $e->getMessage()]);
}
?>