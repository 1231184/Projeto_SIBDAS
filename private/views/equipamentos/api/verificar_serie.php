<?php
require_once __DIR__ . '/../../../includes/funcoes.php';
redirect_if_not_logged();

// Só aceita pedidos GET
if ($_SERVER["REQUEST_METHOD"] !== "GET" || empty($_GET["serie"])) {
    echo json_encode(["existe" => false]);
    exit;
}

$serie = trim($_GET["serie"]);

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("SELECT COUNT(*) FROM equipamentos WHERE numero_serie = :serie");
    $stmt->execute([':serie' => $serie]);
    $existe = $stmt->fetchColumn() > 0;

    $ligacao = null;

    header('Content-Type: application/json');
    echo json_encode(["existe" => $existe]);

} catch (PDOException $err) {
    header('Content-Type: application/json');
    echo json_encode(["existe" => false, "erro" => "Erro na ligação à BD"]);
}
exit;