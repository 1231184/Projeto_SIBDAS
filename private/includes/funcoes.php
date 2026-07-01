<?php
require_once __DIR__ . '/../../config/config.php';

// Inicia a sessão se ainda não estiver iniciada
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verifica se a sessão do utilizador está ativa
function check_session() {
    return isset($_SESSION['utilizador']);
}

// Redireciona automaticamente se não houver sessão iniciada
function redirect_if_not_logged($redirect_to = '/login/login.php') {
    start_session();
    if (!check_session()) {
        header("Location: " . BASE_URL . $redirect_to);
        exit;
    }
}

// Redireciona se o perfil não estiver na lista de perfis permitidos
function redirect_if_not_profile(array $perfis_permitidos) {
    start_session();
    $perfil_atual = $_SESSION['perfil'] ?? '';
    if (!in_array($perfil_atual, $perfis_permitidos, true)) {
        header('Location: ' . BASE_URL . '/private/views/dashboard/dashboard.php');
        exit;
    }
}

// ============================================================
// Encriptação e desencriptação de IDs com OpenSSL (Ficha 13)
// Objectivo: evitar que IDs numéricos fiquem visíveis no HTML
// e possam ser manipulados pelo utilizador.
// ============================================================
function aes_encrypt($value) {
    return bin2hex(openssl_encrypt(
        $value,
        AES_METHOD,
        AES_KEY,
        OPENSSL_RAW_DATA,
        AES_IV
    ));
}

function aes_decrypt($value) {
    if (!is_string($value) || strlen($value) % 2 !== 0) return false;
    return openssl_decrypt(
        hex2bin($value),
        AES_METHOD,
        AES_KEY,
        OPENSSL_RAW_DATA,
        AES_IV
    );
}

// Destrói a sessão e redireciona
function logout_and_redirect($redirect_to = '/login/login.php') {
    start_session();
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . $redirect_to);
    exit;
}
?>