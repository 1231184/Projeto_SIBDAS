<?php
require_once __DIR__ . '/../../../includes/funcoes.php';
require_once __DIR__ . '/../../../../config/config.php';

redirect_if_not_logged();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido.']);
    exit;
}

$id_equipamento = (int)($_POST['id_equipamento'] ?? 0);
if (!$id_equipamento) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID do equipamento não fornecido.']);
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("UPDATE equipamentos SET estado = 'Abatido' WHERE id_equipamento = :id");
    $stmt->execute([':id' => $id_equipamento]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Equipamento não encontrado.']);
        exit;
    }

    echo json_encode(['sucesso' => true]);

} catch (PDOException $err) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $err->getMessage()]);
}

$ligacao = null;