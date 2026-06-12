<?php
// projeto_SIBDAS/login/logout.php

// Puxa as funções (recua 1 pasta para a raiz, depois entra em private/includes)
require_once __DIR__ . '/../private/includes/funcoes.php';

// Chama a função que destrói a sessão e manda para o login.php
logout_and_redirect();
?>