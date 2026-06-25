<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../includes/funcoes.php';

header('Content-Type: application/json');
redirect_if_not_logged();

if (!isset($_GET['id_piso']) || !is_numeric($_GET['id_piso'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID de piso inválido.']);
    exit;
}

$id_piso = (int)$_GET['id_piso'];

try {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Para cada serviço, conta salas e equipamentos por estado
    $sql = "
        SELECT
            s.id_servico,
            s.id_piso,
            s.nome,
            s.centro_custo,
            s.diretor_responsavel,
            s.observacoes,
            (
                SELECT COUNT(*)
                FROM salas sl
                WHERE sl.id_servico = s.id_servico
            ) AS total_salas,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico
            ) AS total_equipamentos,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico
                AND eq.estado = 'Ativo'
            ) AS total_ativos,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico
                AND eq.estado = 'Em Manutenção'
            ) AS total_manutencao,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                WHERE eq.id_servico = s.id_servico
                AND eq.criticidade = 'Suporte de Vida'
            ) AS total_criticos
        FROM servicos s
        WHERE s.id_piso = :id_piso
        ORDER BY s.nome ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_piso', $id_piso, PDO::PARAM_INT);
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $servicos]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $e->getMessage()]);
}
?>