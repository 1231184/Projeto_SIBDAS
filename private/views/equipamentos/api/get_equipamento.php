<?php
// 1. Incluir configurações e funções
require_once __DIR__ . '/../../../includes/funcoes.php';
// Requerer também o config.php se não estiver dentro do funcoes.php
require_once __DIR__ . '/../../../../config/config.php';

// Segurança: Verificar se está logado
redirect_if_not_logged();

// Definir que a resposta deste ficheiro será um JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Verificar se o ID foi enviado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID não fornecido.']);
    exit;
}

$id_equipamento = (int)$_GET['id'];

// 3. Ligar à Base de Dados e ir buscar a informação
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL (Prevenção de SQL Injection com prepare)
    $query = "SELECT * FROM equipamentos WHERE id_equipamento = :id LIMIT 1";
    $stmt = $ligacao->prepare($query);
    $stmt->execute([':id' => $id_equipamento]);

    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($equipamento) {
        // Se encontrou, devolve SUCESSO e os DADOS
        echo json_encode([
            'sucesso' => true,
            'dados' => $equipamento
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Equipamento não encontrado na base de dados.']);
    }
} catch (PDOException $err) {
    // Em caso de erro na BD
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro na BD: ' . $err->getMessage()
    ]);
}

// Fechar ligação
$ligacao = null;
