<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../includes/funcoes.php';

header('Content-Type: application/json');
redirect_if_not_logged();

try {
    $pdo = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME, MYSQL_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Para cada edifício, conta pisos, serviços e equipamentos
    $sql = "
        SELECT
            e.id_edificio,
            e.nome,
            e.descricao,
            e.observacoes,
            (
                SELECT COUNT(*)
                FROM pisos p
                WHERE p.id_edificio = e.id_edificio
            ) AS total_pisos,
            (
                SELECT COUNT(*)
                FROM servicos s
                INNER JOIN pisos p ON s.id_piso = p.id_piso
                WHERE p.id_edificio = e.id_edificio
            ) AS total_servicos,
            (
                SELECT COUNT(*)
                FROM equipamentos eq
                INNER JOIN servicos s ON eq.id_servico = s.id_servico
                INNER JOIN pisos p   ON s.id_piso = p.id_piso
                WHERE p.id_edificio = e.id_edificio
            ) AS total_equipamentos
        FROM edificios e
        ORDER BY e.nome ASC
    ";

    $stmt = $pdo->query($sql);
    $edificios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $edificios]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $e->getMessage()]);
}
?>