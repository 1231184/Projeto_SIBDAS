<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../includes/funcoes.php';

header('Content-Type: application/json');
redirect_if_not_logged();

if (!isset($_GET['id_edificio']) || !is_numeric($_GET['id_edificio'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID de edifício inválido.']);
    exit;
}

$id_edificio = (int)$_GET['id_edificio'];

try {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $sql = "
        SELECT
            p.id_piso,
            p.id_edificio,
            p.designacao,
            p.observacoes,
            (
                SELECT COUNT(*)
                FROM servicos s
                WHERE s.id_piso = p.id_piso
            ) AS total_servicos,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                INNER JOIN servicos s ON eq.id_servico = s.id_servico
                WHERE s.id_piso = p.id_piso
            ) AS total_equipamentos
        FROM pisos p
        WHERE p.id_edificio = :id_edificio
        ORDER BY p.designacao ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_edificio', $id_edificio, PDO::PARAM_INT);
    $stmt->execute();
    $pisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $pisos]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $e->getMessage()]);
}
?>