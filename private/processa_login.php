<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../login/login.php');
    return;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/funcoes.php';

// Inicia a sessão para poder usar $_SESSION
session_start();


$username = isset($_POST['text_username']) ? trim($_POST['text_username']) : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';


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


if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    $_SESSION['last_username'] = $username;
    registar_log('login_falhado', $username, null, 'Dados inválidos: ' . implode('; ', $validation_errors));
    header('Location: ../login/login.php');
    return;
}


try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );


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


    if (!$utilizador) {
        registar_log('login_falhado', $username, null, 'Email não registado na base de dados');
        $_SESSION['server_error'] = 'O email introduzido não está registado.';
        $_SESSION['last_username'] = $username;
        header('Location: ../login/login.php');
        return;
    }

    if (!password_verify($password, $utilizador->password_hash)) {
        registar_log('login_falhado', $username, (int)$utilizador->id_utilizador, 'Password incorreta');
        $_SESSION['server_error'] = 'A password introduzida está incorreta.';
        $_SESSION['last_username'] = $username;
        header('Location: ../login/login.php');
        return;
    }


    $_SESSION['utilizador']    = $utilizador->email_decrypted;
    $_SESSION['nome']          = $utilizador->nome;
    $_SESSION['perfil']        = $utilizador->perfil;
    $_SESSION['id_utilizador'] = $utilizador->id_utilizador;


    unset($_SESSION['last_username']);


    registar_log('login_sucesso', $username, (int)$utilizador->id_utilizador, 'Login efetuado com sucesso — perfil: ' . $utilizador->perfil);

    $ligacao = null;

} catch (PDOException $e) {
    registar_log('login_falhado', $username, null, 'Erro na BD: ' . $e->getMessage());
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ../login/login.php');
    return;
}

// Redireciona para o dashboard
header('Location: views/dashboard/dashboard.php');
exit;