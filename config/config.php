<?php
// Configurações globais da aplicação
define('APP_NAME', 'MedStock Solutions');
define('APP_VERSION', '1.0.0');
define('APP_COPYRIGHT', '&copy; 2026 ISEP - LEBIOM');

// Caminho Base do Projeto (Ficha 11 - Página 4)
define('BASE_URL', '/projeto_SIBDAS');

// DADOS DE ACESSO À BASE DE DADOS (Ficha 11 - Página 10)
define('MYSQL_HOST', 'vsgate-s1.dei.isep.ipp.pt');
define('MYSQL_PORT', '10464');
define('MYSQL_DATABASE', 'db1231184');
define('MYSQL_USERNAME', '1231184');  
define('MYSQL_PASSWORD', 'almeida_184'); 

// ----------------------------------------------------------------
// Segurança – Encriptação com OpenSSL (Ficha 13 - Página 7)
// ----------------------------------------------------------------
define('AES_METHOD', 'AES-256-CBC');
define('AES_KEY',    'H0SDRQzIGqclX2kbYBk9xspdn9U5f3Wa');
define('AES_IV',     'BzKAbjuREsHgnw56');
?>