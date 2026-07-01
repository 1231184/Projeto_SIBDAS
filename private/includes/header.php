<?php
require_once __DIR__ . '/../../config/config.php';
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>

    <link rel="stylesheet" href="/sibdas/1231184/medstock-solutions/assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/sibdas/1231184/medstock-solutions/assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="/sibdas/1231184/medstock-solutions/assets/css/1231184.css">

    <script src="<?= BASE_URL ?>/assets/jquery/jquery-3.6.0.min.js"></script>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/datatables/datatables.min.css">
    <script src="<?= BASE_URL ?>/assets/datatables/datatables.min.js"></script>
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>

<?php if (isset($pagina) && $pagina === 'login'): ?>

    <body class="bg-login-page d-flex flex-column align-items-center justify-content-center min-vh-100 p-3">

<?php else: ?>

    <body class="d-flex vh-100 overflow-hidden bg-backend">

<?php endif; ?>