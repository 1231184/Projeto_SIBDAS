<?php
// medstock-solutions/login/logout.php

require_once __DIR__ . '/../private/includes/funcoes.php';

// Iniciar sessão para aceder aos dados do utilizador antes de destruir
start_session();

// Registar logout antes de destruir a sessão
if (isset($_SESSION['utilizador'])) {
    registar_log(
        'logout',
        $_SESSION['utilizador'],
        $_SESSION['id_utilizador'] ?? null,
        'Sessão terminada pelo utilizador'
    );
}

// Destruir sessão e redirecionar para o login
logout_and_redirect();
?>