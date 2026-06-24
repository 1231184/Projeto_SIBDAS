<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../private/includes/funcoes.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID não fornecido.']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $ligacao = new PDO("mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ficha 13: Buscar os dados através do ID passado por GET
    $sql = "SELECT * FROM fornecedores WHERE id_fornecedor = :id LIMIT 1";
    $stmt = $ligacao->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fornecedor) {
        echo json_encode(['sucesso' => true, 'dados' => $fornecedor]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Fornecedor não encontrado.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $e->getMessage()]);
}
?>