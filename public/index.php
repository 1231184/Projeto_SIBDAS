<?php
require_once __DIR__ . '/../config/config.php';

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rows = $ligacao->query("SELECT chave, valor FROM conteudos_site")->fetchAll(PDO::FETCH_KEY_PAIR);
    $ligacao = null;
} catch (PDOException $err) {
    $rows = [];
}

function c(string $chave, array $rows): string {
    return htmlspecialchars($rows[$chave] ?? '', ENT_QUOTES);
}
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedStock Solutions - Gestão de Equipamentos</title>

    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../assets/css/1231184.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="../assets/img/logotipo.png" alt="MedStock Solutions" style="height: 60px;">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-2 gap-lg-4 text-center">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold nav-hover" href="#sobre">Sobre Nós</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold nav-hover" href="#servicos">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold nav-hover" href="#funcionalidades">Funcionalidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold nav-hover" href="#contacto">Contacto</a>
                    </li>
                </ul>
                <div class="d-flex justify-content-center mt-3 mt-lg-0">
                    <a href="../login/login.php"
                        class="btn btn-brand d-flex align-items-center gap-2 px-4 py-2 shadow-sm rounded-3">
                        Área Reservada
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.9rem;"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero-section position-relative"
            style="padding-top: 100px; padding-bottom: 120px; background-image: linear-gradient(rgba(5, 15, 30, 0.82), rgba(5, 15, 30, 0.88)), url('../assets/img/fundo.png');">
            <div class="hero-bg-glow-1 position-absolute rounded-circle"></div>
            <div class="hero-bg-glow-2 position-absolute rounded-circle"></div>

            <div class="container position-relative z-1 py-3">
                <div class="row">
                    <div class="col-lg-8 col-md-10">

    <h1 class="hero-title fw-bold text-white mb-4 fade-up fade-up-delay-1"><?= c('hero_titulo', $rows) ?></h1>

    <p class="lead text-light mb-4 fs-5 fade-up fade-up-delay-2" style="max-width: 600px; color: #cbd5e1 !important;">
        <?= c('hero_subtitulo', $rows) ?>
    </p>
</div>
                </div>
            </div>
        </section>

        <section class="bg-brand py-5">
            <div class="container">
                <div class="row g-4 text-white text-center">
                    <div class="col-6 col-md-3">
                        <div class="fs-1 fw-bold metric-value"><?= c('metrica1_valor', $rows) ?></div>
                        <div class="fs-6 opacity-75 mt-1"><?= c('metrica1_label', $rows) ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="fs-1 fw-bold metric-value"><?= c('metrica2_valor', $rows) ?></div>
                        <div class="fs-6 opacity-75 mt-1"><?= c('metrica2_label', $rows) ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="fs-1 fw-bold metric-value"><?= c('metrica3_valor', $rows) ?></div>
                        <div class="fs-6 opacity-75 mt-1"><?= c('metrica3_label', $rows) ?></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="fs-1 fw-bold metric-value"><?= c('metrica4_valor', $rows) ?></div>
                        <div class="fs-6 opacity-75 mt-1"><?= c('metrica4_label', $rows) ?></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="sobre" class="py-5 bg-light">
            <div class="container py-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <h2 class="fw-bold mb-4 fs-2 text-dark"><?= c('sobre_titulo', $rows) ?></h2>
                        <p class="text-muted mb-4 lh-base"><?= c('sobre_texto', $rows) ?></p>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                            <?php foreach (explode(',', $rows['sobre_topicos'] ?? '') as $topico): ?>
                            <li class="d-flex align-items-center gap-3 text-secondary">
                                <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                                <span><?= htmlspecialchars(trim($topico)) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover-elevate">
                                    <div class="card-body p-4">
                                        <div class="bg-brand-subtle text-brand rounded-3 p-2 mb-3 d-inline-flex">
                                            <i class="fa-solid fa-shield-halved fs-5 px-1"></i>
                                        </div>
                                        <h5 class="fw-bold fs-6 mb-1 text-dark">Segurança</h5>
                                        <p class="text-muted small mb-0">Dados protegidos e encriptados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover-elevate">
                                    <div class="card-body p-4">
                                        <div class="bg-brand-subtle text-brand rounded-3 p-2 mb-3 d-inline-flex">
                                            <i class="fa-solid fa-clock fs-5 px-1"></i>
                                        </div>
                                        <h5 class="fw-bold fs-6 mb-1 text-dark">Rastreabilidade</h5>
                                        <p class="text-muted small mb-0">Consulta rápida do estado e localização de cada
                                            equipamento</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover-elevate">
                                    <div class="card-body p-4">
                                        <div class="bg-brand-subtle text-brand rounded-3 p-2 mb-3 d-inline-flex">
                                            <i class="fa-solid fa-building fs-5 px-1"></i>
                                        </div>
                                        <h5 class="fw-bold fs-6 mb-1 text-dark">Multi-unidade</h5>
                                        <p class="text-muted small mb-0">Gerencie vários hospitais</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover-elevate">
                                    <div class="card-body p-4">
                                        <div class="bg-brand-subtle text-brand rounded-3 p-2 mb-3 d-inline-flex">
                                            <i class="fa-solid fa-award fs-5 px-1"></i>
                                        </div>
                                        <h5 class="fw-bold fs-6 mb-1 text-dark">Qualidade</h5>
                                        <p class="text-muted small mb-0">Soluções certificadas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicos" class="py-5 bg-white">
            <div class="container-fluid py-5 px-4 px-lg-5" style="max-width: 1280px; margin: 0 auto;">

                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-3 fs-2 text-dark"><?= c('servicos_titulo', $rows) ?></h2>
                    <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                        <?= c('servicos_subtitulo', $rows) ?>
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="card border border-light shadow-sm rounded-4 h-100 card-hover-elevate">
                            <div class="card-body p-4">
                                <div class="bg-brand-subtle text-brand rounded-3 p-3 mb-4 d-inline-flex">
                                    <i class="fa-solid fa-clipboard-list fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Inventário Completo</h5>
                                <p class="text-muted small mb-0 lh-lg">Registo detalhado de todos os equipamentos com
                                    dados técnicos, localização, estado e criticidade clínica.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border border-light shadow-sm rounded-4 h-100 card-hover-elevate">
                            <div class="card-body p-4">
                                <div class="bg-brand-subtle text-brand rounded-3 p-3 mb-4 d-inline-flex">
                                    <i class="fa-solid fa-folder-open fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Gestão Documental</h5>
                                <p class="text-muted small mb-0 lh-lg">Centralização de manuais, certificados de
                                    calibração, contratos e relatórios de manutenção.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border border-light shadow-sm rounded-4 h-100 card-hover-elevate">
                            <div class="card-body p-4">
                                <div class="bg-brand-subtle text-brand rounded-3 p-3 mb-4 d-inline-flex">
                                    <i class="fa-solid fa-file-contract fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Garantias e Contratos</h5>
                                <p class="text-muted small mb-0 lh-lg">Acompanhamento de garantias e contratos de
                                    manutenção com alertas automáticos de expiração.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border border-light shadow-sm rounded-4 h-100 card-hover-elevate">
                            <div class="card-body p-4">
                                <div class="bg-brand-subtle text-brand rounded-3 p-3 mb-4 d-inline-flex">
                                    <i class="fa-solid fa-map-pin fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Localização por Serviço</h5>
                                <p class="text-muted small mb-0 lh-lg">Consulta imediata da localização atual de cada
                                    equipamento, organizada por edifício, piso, serviço e sala.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border border-light shadow-sm rounded-4 h-100 card-hover-elevate">
                            <div class="card-body p-4">
                                <div class="bg-brand-subtle text-brand rounded-3 p-3 mb-4 d-inline-flex">
                                    <i class="fa-solid fa-chart-pie fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Relatórios e Dashboard</h5>
                                <p class="text-muted small mb-0 lh-lg">Painéis de controlo com indicadores chave,
                                    gráficos e exportação de relatórios.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border border-light shadow-sm rounded-4 h-100 card-hover-elevate">
                            <div class="card-body p-4">
                                <div class="bg-brand-subtle text-brand rounded-3 p-3 mb-4 d-inline-flex">
                                    <i class="fa-solid fa-truck-medical fs-4"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Gestão de Fornecedores</h5>
                                <p class="text-muted small mb-0 lh-lg">Registo de fabricantes, distribuidores e
                                    prestadores de serviços de assistência técnica.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="funcionalidades" class="py-5 bg-light">
            <div class="container-fluid py-5 px-4 px-lg-5" style="max-width: 1280px; margin: 0 auto;">

                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-3 fs-2 text-dark"><?= c('funcionalidades_titulo', $rows) ?></h2>
                    <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                        <?= c('funcionalidades_subtitulo', $rows) ?>
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Pesquisa avançada por múltiplos critérios</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Alertas automáticos de garantias e documentos</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Controlo de acesso com autenticação segura</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Rastreamento por número de série</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Classificação por criticidade clínica</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Registo do estado atual e evolução de cada equipamento</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Associação de fornecedores por equipamento</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center gap-3 p-4 bg-white rounded-4 border border-light shadow-sm h-100 card-hover-elevate">
                            <i class="fa-regular fa-circle-check text-brand fs-5 flex-shrink-0"></i>
                            <span class="text-secondary small fw-medium">Interface responsiva para mobile e desktop</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contacto" class="py-5 bg-white border-top">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-5">
                        <h2 class="fw-bold mb-4 fs-2 text-dark"><?= c('contactos_titulo', $rows) ?></h2>
                        <p class="text-muted mb-4"><?= c('contactos_texto', $rows) ?></p>
                        <div class="d-flex flex-column gap-4 mt-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-brand-subtle text-brand p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-location-dot fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Sede / Morada</h6>
                                    <span class="text-muted small"><?= c('contactos_morada', $rows) ?></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-brand-subtle text-brand p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-phone fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Telefone (Geral)</h6>
                                    <span class="text-muted small"><?= c('contactos_telefone', $rows) ?></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-brand-subtle text-brand p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-envelope fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">E-mail de Suporte</h6>
                                    <span class="text-muted small"><?= c('contactos_email', $rows) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-7">
                        <div class="card border border-light shadow-sm rounded-4">
                            <div class="card-body p-4 p-lg-5">
                                <h5 class="fw-bold text-dark mb-4">Envie-nos uma mensagem</h5>
                                <form action="#" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="contactoNome" class="form-label small fw-medium text-dark">Nome Completo</label>
<input type="text" id="contactoNome" class="form-control bg-light border-0 shadow-none py-2" placeholder="O seu nome">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contactoEmail" class="form-label small fw-medium text-dark">Email de Contacto</label>
                                            <input type="email" id="contactoEmail" class="form-control bg-light border-0 shadow-none py-2" placeholder="exemplo@hospital.pt">
                                        </div>
                                        <div class="col-12">
                                            <label for="contactoUnidade" class="form-label small fw-medium text-dark">Unidade de Saúde (Opcional)</label>
                                            <input type="text" id="contactoUnidade" class="form-control bg-light border-0 shadow-none py-2" placeholder="Ex: Hospital de São João">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-medium text-dark">A sua Mensagem</label>
                                            <textarea class="form-control bg-light border-0 shadow-none py-2" rows="5" placeholder="Como o podemos ajudar?"></textarea>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-brand w-100 py-2 fw-bold shadow-sm rounded-3">Enviar Mensagem <i class="fa-solid fa-paper-plane ms-1"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4 mb-4 pb-4 border-bottom border-secondary">
                <div class="col-lg-4 pe-lg-5">
                    <img src="../assets/img/logotipo.png" alt="MedStock Logo" style="height: 45px; filter: brightness(0) invert(1);" class="mb-4">
                    <p class="text-white-50 small mb-0 lh-lg"><?= c('rodape_texto', $rows) ?></p>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Links Rápidos</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 small">
                        <li><a href="#sobre" class="text-white-50 text-decoration-none nav-hover">Sobre a Empresa</a></li>
                        <li><a href="#servicos" class="text-white-50 text-decoration-none nav-hover">Os Nossos Serviços</a></li>
                        <li><a href="#funcionalidades" class="text-white-50 text-decoration-none nav-hover">Funcionalidades do Sistema</a></li>
                        <li><a href="#contacto" class="text-white-50 text-decoration-none nav-hover">Falar com o Suporte</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-3 text-uppercase mt-2" style="letter-spacing: 1px; font-size: 0.85rem;">Siga-nos</h6>
                    <div class="d-flex gap-2">
                        <a href="<?= c('rodape_linkedin', $rows) ?: '#' ?>" class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 text-white rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="<?= c('rodape_github', $rows) ?: '#' ?>" class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 text-white rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fa-brands fa-github"></i></a>
                        <a href="<?= c('rodape_twitter', $rows) ?: '#' ?>" class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 text-white rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 pt-2 mt-4">
                <span class="text-white-50 small"><?= c('rodape_copyright', $rows) ?></span>
            </div>
        </div>
    </footer>

    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/1231184.js"></script>
</body>

</html>