<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../includes/funcoes.php';

header('Content-Type: application/json');

// Ficha 13 - Passo 1: proteger o acesso com sessão ativa
redirect_if_not_logged();

// Ficha 13 - Passo 3: obter e validar o ID enviado via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID não fornecido ou inválido.']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlForn = "SELECT * FROM fornecedores WHERE id_fornecedor = :id LIMIT 1";
    $stmtForn = $ligacao->prepare($sqlForn);
    $stmtForn->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtForn->execute();
    $fornecedor = $stmtForn->fetch(PDO::FETCH_ASSOC);

    if (!$fornecedor) {
        echo json_encode(['sucesso' => false, 'erro' => 'Fornecedor não encontrado.']);
        exit;
    }

    $sqlEq = "
        SELECT
            e.id_equipamento,
            e.codigo_interno,
            e.designacao,
            e.estado,
            ef.papel AS relacao,
            s.nome   AS nome_servico
        FROM equipamentos e
        INNER JOIN equipamento_fornecedor ef ON e.id_equipamento = ef.id_equipamento
        LEFT JOIN  servicos s               ON e.id_servico      = s.id_servico
        WHERE ef.id_fornecedor = :id

        UNION

        SELECT
            e.id_equipamento,
            e.codigo_interno,
            e.designacao,
            e.estado,
            'Fabricante' AS relacao,
            s.nome       AS nome_servico
        FROM equipamentos e
        LEFT JOIN servicos s ON e.id_servico = s.id_servico
        WHERE e.id_fabricante = :id
          AND e.id_equipamento NOT IN (
              SELECT id_equipamento
              FROM   equipamento_fornecedor
              WHERE  id_fornecedor = :id
          )

        ORDER BY codigo_interno ASC
    ";

    $stmtEq = $ligacao->prepare($sqlEq);
    $stmtEq->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtEq->execute();
    $equipamentos = $stmtEq->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;

    echo json_encode([
        'sucesso'      => true,
        'dados'        => $fornecedor,
        'equipamentos' => array_map(function($eq) {
            $eq['id_equipamento_enc'] = aes_encrypt($eq['id_equipamento']);
            return $eq;
        }, $equipamentos)
    ]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro na BD: ' . $e->getMessage()]);
}
?>