<?php
// --------------------------------------------------------------------
// SEGURANÇA: Impede acesso direto a este script via URL.
// Só deve ser acedido após submissão do formulário (POST).
// Se for acedido diretamente, redireciona para o login.
// --------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../login/login.php');
    return;
}

// Inicia a sessão para poder usar $_SESSION
session_start();

// --------------------------------------------------------------------
// RECOLHA DE DADOS DO FORMULÁRIO
// --------------------------------------------------------------------
$username = isset($_POST['text_username']) ? $_POST['text_username'] : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

// --------------------------------------------------------------------
// VALIDAÇÃO DOS DADOS
// --------------------------------------------------------------------
$validation_errors = [];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O username tem que ser um email válido.';
}

if (strlen($username) < 5 || strlen($username) > 50) {
    $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres.';
}

if (strlen($password) < 6 || strlen($password) > 12) {
    $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres.';
}

// Se houver erros, guarda-os na sessão e redireciona para o login
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../login/login.php');
    return;
}

// --------------------------------------------------------------------
// SIMULAÇÃO DE RESULTADO DE LOGIN (antes da ligação real à base de dados)
// --------------------------------------------------------------------
$result['status'] = 1; // 1 = login válido, 0 = inválido

// Verifica se o login é inválido
if (!$result['status']) {
    $_SESSION['server_error'] = 'Login inválido';
    header('Location: ../login/login.php');
    return;
}

// --------------------------------------------------------------------
// LOGIN BEM-SUCEDIDO: Guardar o utilizador na sessão
// --------------------------------------------------------------------
$_SESSION['utilizador'] = $username;

// Redireciona para o dashboard
header('Location: views/dashboard/dashboard.php');
exit;