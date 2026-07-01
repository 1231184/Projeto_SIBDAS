<?php
// --------------------------------------------------------------------
// SEGURANÇA: Impede acesso direto a este script via URL.
// Só deve ser acedido após submissão do formulário (POST).
// --------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../login/login.php');
    return;
}

require_once __DIR__ . '/../config/config.php';

// Inicia a sessão para poder usar $_SESSION
session_start();

// --------------------------------------------------------------------
// RECOLHA DE DADOS DO FORMULÁRIO
// --------------------------------------------------------------------
$username = isset($_POST['text_username']) ? trim($_POST['text_username']) : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

// --------------------------------------------------------------------
// VALIDAÇÃO DOS DADOS
// --------------------------------------------------------------------
$validation_errors = [];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O username tem que ser um email válido.';
}

if (strlen($username) < 5 || strlen($username) > 100) {
    $validation_errors[] = 'O username deve ter entre 5 e 100 caracteres.';
}

if (strlen($password) < 6) {
    $validation_errors[] = 'A password deve ter pelo menos 6 caracteres.';
}

// Se houver erros de validação, redireciona com mensagem (mantém o email)
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    $_SESSION['last_username'] = $username;
    header('Location: ../login/login.php');
    return;
}

// --------------------------------------------------------------------
// VERIFICAÇÃO REAL NA BASE DE DADOS (Ficha 14 - Secção 3)
// --------------------------------------------------------------------
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Procurar o utilizador com AES_DECRYPT no email (Ficha 14 - página 20)
    $comando = $ligacao->prepare("
        SELECT *, AES_DECRYPT(email, :chave) AS email_decrypted 
        FROM utilizadores 
        WHERE AES_DECRYPT(email, :chave) = :u
    ");
    $comando->execute([
        ':chave' => MYSQL_AES_KEY,
        ':u'     => $username
    ]);
    $utilizador = $comando->fetch(PDO::FETCH_OBJ);

    // Verificar se o email existe na BD
    if (!$utilizador) {
        $_SESSION['server_error'] = 'O email introduzido não está registado.';
        $_SESSION['last_username'] = $username;
        header('Location: ../login/login.php');
        return;
    }

    // Verificar se a password está correta (bcrypt)
    if (!password_verify($password, $utilizador->password_hash)) {
        $_SESSION['server_error'] = 'A password introduzida está incorreta.';
        $_SESSION['last_username'] = $username;
        header('Location: ../login/login.php');
        return;
    }

    // --------------------------------------------------------------------
    // LOGIN BEM-SUCEDIDO: Guardar dados na sessão (Ficha 14 - página 20)
    // --------------------------------------------------------------------
    $_SESSION['utilizador']    = $utilizador->email_decrypted; // valor desencriptado
    $_SESSION['nome']          = $utilizador->nome;
    $_SESSION['perfil']        = $utilizador->perfil;
    $_SESSION['id_utilizador'] = $utilizador->id_utilizador;

    // Limpar last_username da sessão após login bem-sucedido
    unset($_SESSION['last_username']);

    $ligacao = null;

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ../login/login.php');
    return;
}

// Redireciona para o dashboard
header('Location: views/dashboard/dashboard.php');
exit;