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

// ============================================================
// Registo de eventos de autenticação na tabela log_acessos
// (Guia de Submissão - secção 3.3.5)
// ============================================================
function registar_log(string $tipo_evento, ?string $email = null, ?int $id_utilizador = null, ?string $detalhe = null) {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = $ligacao->prepare("
            INSERT INTO log_acessos (email, id_utilizador, tipo_evento, ip_address, detalhe)
            VALUES (:email, :id_utilizador, :tipo_evento, :ip, :detalhe)
        ");
        $stmt->execute([
            ':email'        => $email,
            ':id_utilizador' => $id_utilizador,
            ':tipo_evento'  => $tipo_evento,
            ':ip'           => $ip,
            ':detalhe'      => $detalhe
        ]);
        $ligacao = null;
    } catch (PDOException $e) {
        // Silencioso — o log não deve interromper o fluxo principal
    }
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