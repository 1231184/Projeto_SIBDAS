<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

redirect_if_not_profile(['Administrador']);

$sucesso = '';
$erro    = '';

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- GUARDAR (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $campos = [
            'hero_titulo', 'hero_subtitulo',
            'metrica1_valor', 'metrica1_label', 'metrica2_valor', 'metrica2_label',
            'metrica3_valor', 'metrica3_label', 'metrica4_valor', 'metrica4_label',
            'sobre_titulo', 'sobre_texto', 'sobre_topicos',
            'servicos_titulo', 'servicos_subtitulo',
            'servico1_titulo', 'servico1_descricao',
            'servico2_titulo', 'servico2_descricao',
            'servico3_titulo', 'servico3_descricao',
            'servico4_titulo', 'servico4_descricao',
            'servico5_titulo', 'servico5_descricao',
            'servico6_titulo', 'servico6_descricao',
            'funcionalidades_titulo', 'funcionalidades_subtitulo',
            'func1', 'func2', 'func3', 'func4', 'func5', 'func6', 'func7', 'func8',
            'contactos_titulo', 'contactos_texto', 'contactos_morada', 'contactos_telefone', 'contactos_email',
            'rodape_texto', 'rodape_linkedin', 'rodape_github', 'rodape_twitter', 'rodape_copyright'
        ];

        $stmt = $ligacao->prepare("
            UPDATE conteudos_site
            SET valor = :valor, data_atualizacao = NOW(), id_utilizador = :id_utilizador
            WHERE chave = :chave
        ");

        $id_utilizador = $_SESSION['utilizador']['id_utilizador'] ?? null;

        foreach ($campos as $chave) {
            $stmt->execute([
                ':valor'        => $_POST[$chave] ?? '',
                ':chave'        => $chave,
                ':id_utilizador' => $id_utilizador
            ]);
        }
        $sucesso = 'Conteúdo guardado com sucesso!';
    }

    // --- LER CONTEÚDOS ---
    $rows = $ligacao->query("SELECT chave, valor FROM conteudos_site")->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $err) {
    $erro = 'Erro na base de dados: ' . $err->getMessage();
    $rows = [];
}
$ligacao = null;

// Helper para imprimir valor com segurança
function c(string $chave, array $rows): string {
    return htmlspecialchars($rows[$chave] ?? '', ENT_QUOTES);
}
?>

<?php include '../../includes/header.php'; ?>

    <?php include '../../includes/sidebar.php'; ?>

    <!-- MAIN -->
    <main class="flex-grow-1 overflow-auto p-4 p-md-5">

        <header class="d-md-none d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-stethoscope fs-5 text-brand"></i>
                <h1 class="h5 fw-bold mb-0 text-dark">MedStock</h1>
            </div>
            <button class="btn btn-light border-0 shadow-sm"><i class="fa-solid fa-bars"></i></button>
        </header>

        <div class="d-flex align-items-center justify-content-between gap-3 mb-4" style="max-width: 1024px; margin: 0 auto;">
            <div>
                <h1 class="h3 fw-bold text-dark mb-0">Gestão de Conteúdo</h1>
                <p class="text-muted small mt-1 mb-0">Edite as informações apresentadas na área pública do website institucional.</p>
            </div>
            <button type="button" class="btn btn-outline-brand fw-bold shadow-sm d-flex align-items-center gap-2 flex-shrink-0"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasPreview">
                <i class="fa-solid fa-eye"></i> Ver Preview
            </button>
        </div>

        <?php if ($sucesso): ?>
            <div class="alert alert-success shadow-sm mb-4" style="max-width: 1024px; margin: 0 auto;"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="alert alert-danger shadow-sm mb-4" style="max-width: 1024px; margin: 0 auto;"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form action="#" method="POST" style="max-width: 1024px; margin: 0 auto;">

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        Secção: Cabeçalho Principal
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Título de Destaque</label>
                            <input type="text" name="hero_titulo" class="form-control shadow-sm bg-white" value="<?= c('hero_titulo', $rows) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Introdutório (Lead)</label>
                            <textarea name="hero_subtitulo" class="form-control shadow-sm bg-white" rows="2"><?= c('hero_subtitulo', $rows) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        Secção: Métricas
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 1</label>
                            <input type="text" name="metrica1_valor" class="form-control shadow-sm bg-white mb-2" value="<?= c('metrica1_valor', $rows) ?>">
                            <input type="text" name="metrica1_label" class="form-control shadow-sm bg-light" value="<?= c('metrica1_label', $rows) ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 2</label>
                            <input type="text" name="metrica2_valor" class="form-control shadow-sm bg-white mb-2" value="<?= c('metrica2_valor', $rows) ?>">
                            <input type="text" name="metrica2_label" class="form-control shadow-sm bg-light" value="<?= c('metrica2_label', $rows) ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 3</label>
                            <input type="text" name="metrica3_valor" class="form-control shadow-sm bg-white mb-2" value="<?= c('metrica3_valor', $rows) ?>">
                            <input type="text" name="metrica3_label" class="form-control shadow-sm bg-light" value="<?= c('metrica3_label', $rows) ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 4</label>
                            <input type="text" name="metrica4_valor" class="form-control shadow-sm bg-white mb-2" value="<?= c('metrica4_valor', $rows) ?>">
                            <input type="text" name="metrica4_label" class="form-control shadow-sm bg-light" value="<?= c('metrica4_label', $rows) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        <i class="fa-solid fa-users text-brand me-2"></i>Secção: Sobre Nós
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Título da Secção</label>
                            <input type="text" name="sobre_titulo" class="form-control shadow-sm bg-white" value="<?= c('sobre_titulo', $rows) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Descritivo</label>
                            <textarea name="sobre_texto" class="form-control shadow-sm bg-white" rows="3"><?= c('sobre_texto', $rows) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Tópicos de Destaque (Separados por vírgula)</label>
                            <textarea name="sobre_topicos" class="form-control shadow-sm bg-white" rows="2"><?= c('sobre_topicos', $rows) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        <i class="fa-solid fa-layer-group text-brand me-2"></i>Secção: Serviços
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1 text-muted">Título da Secção</label>
                            <input type="text" name="servicos_titulo" class="form-control shadow-sm bg-white" value="<?= c('servicos_titulo', $rows) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1 text-muted">Subtítulo</label>
                            <input type="text" name="servicos_subtitulo" class="form-control shadow-sm bg-white" value="<?= c('servicos_subtitulo', $rows) ?>">
                        </div>
                    </div>
                    <hr class="mb-4">
                    <div class="row g-4">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1 text-muted">Cartão <?= $i ?></label>
                            <input type="text" name="servico<?= $i ?>_titulo" class="form-control shadow-sm bg-white mb-2" placeholder="Título" value="<?= c('servico'.$i.'_titulo', $rows) ?>">
                            <textarea name="servico<?= $i ?>_descricao" class="form-control shadow-sm bg-white" rows="2" placeholder="Descrição"><?= c('servico'.$i.'_descricao', $rows) ?></textarea>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        <i class="fa-solid fa-layer-group text-brand me-2"></i>Secção: Funcionalidades
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1 text-muted">Título da Secção</label>
                            <input type="text" name="funcionalidades_titulo" class="form-control shadow-sm bg-white" value="<?= c('funcionalidades_titulo', $rows) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1 text-muted">Subtítulo</label>
                            <input type="text" name="funcionalidades_subtitulo" class="form-control shadow-sm bg-white" value="<?= c('funcionalidades_subtitulo', $rows) ?>">
                        </div>
                    </div>
                    <hr class="mb-4">
                    <div class="row g-3">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1 text-muted">Item <?= $i ?></label>
                            <input type="text" name="func<?= $i ?>" class="form-control shadow-sm bg-white" value="<?= c('func'.$i, $rows) ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        <i class="fa-solid fa-address-book text-brand me-2"></i>Secção: Contactos
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Título da Secção</label>
                            <input type="text" name="contactos_titulo" class="form-control shadow-sm bg-white" value="<?= c('contactos_titulo', $rows) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Introdutório</label>
                            <textarea name="contactos_texto" class="form-control shadow-sm bg-white" rows="2"><?= c('contactos_texto', $rows) ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-medium mb-1">Sede / Morada</label>
                            <input type="text" name="contactos_morada" class="form-control shadow-sm bg-white" value="<?= c('contactos_morada', $rows) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1">Telefone (Geral)</label>
                            <input type="text" name="contactos_telefone" class="form-control shadow-sm bg-white" value="<?= c('contactos_telefone', $rows) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1">E-mail de Suporte</label>
                            <input type="email" name="contactos_email" class="form-control shadow-sm bg-white" value="<?= c('contactos_email', $rows) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dash-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-semibold fs-6 mb-0 text-dark">
                        <i class="fa-solid fa-shoe-prints text-brand me-2"></i>Secção: Rodapé (Footer)
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Descritivo Curto</label>
                            <textarea name="rodape_texto" class="form-control shadow-sm bg-white" rows="2"><?= c('rodape_texto', $rows) ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium mb-1">Link LinkedIn</label>
                            <input type="url" name="rodape_linkedin" class="form-control shadow-sm bg-white" value="<?= c('rodape_linkedin', $rows) ?>" placeholder="https://linkedin.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium mb-1">Link GitHub</label>
                            <input type="url" name="rodape_github" class="form-control shadow-sm bg-white" value="<?= c('rodape_github', $rows) ?>" placeholder="https://github.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium mb-1">Link Twitter / X</label>
                            <input type="url" name="rodape_twitter" class="form-control shadow-sm bg-white" value="<?= c('rodape_twitter', $rows) ?>" placeholder="https://twitter.com/...">
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label small fw-medium mb-1">Texto de Copyright</label>
                            <input type="text" name="rodape_copyright" class="form-control shadow-sm bg-white" value="<?= c('rodape_copyright', $rows) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mb-5">
                <button type="submit" class="btn btn-brand d-inline-flex align-items-center gap-2 px-4 shadow-sm fw-bold">
                    <i class="fa-solid fa-save"></i> Guardar Alterações
                </button>
            </div>

        </form>

        <!-- Offcanvas de Preview ao vivo -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPreview" style="width: 480px;">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title fw-bold">Pré-visualização</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">

                <!-- Mini Hero -->
                <div class="p-4 text-white" style="background: linear-gradient(rgba(5,15,30,0.9), rgba(5,15,30,0.95)); min-height: 180px;">
                    <h2 class="fw-bold mb-2" id="preview-hero-titulo" style="font-size: 1.4rem;">—</h2>
                    <p class="small mb-0" style="color: #cbd5e1;" id="preview-hero-subtitulo">—</p>
                </div>

                <!-- Mini Métricas -->
                <div class="bg-brand text-white p-3">
                    <div class="row g-2 text-center">
                        <div class="col-3">
                            <div class="fw-bold" id="preview-metrica1-valor" style="font-size: 0.95rem;">—</div>
                            <div class="text-white-50" style="font-size: 0.8rem;" id="preview-metrica1-label">—</div>
                        </div>
                        <div class="col-3">
                            <div class="fw-bold" id="preview-metrica2-valor" style="font-size: 0.95rem;">—</div>
                            <div class="text-white-50" style="font-size: 0.8rem;" id="preview-metrica2-label">—</div>
                        </div>
                        <div class="col-3">
                            <div class="fw-bold" id="preview-metrica3-valor" style="font-size: 0.95rem;">—</div>
                            <div class="text-white-50" style="font-size: 0.8rem;" id="preview-metrica3-label">—</div>
                        </div>
                        <div class="col-3">
                            <div class="fw-bold" id="preview-metrica4-valor" style="font-size: 0.95rem;">—</div>
                            <div class="text-white-50" style="font-size: 0.8rem;" id="preview-metrica4-label">—</div>
                        </div>
                    </div>
                </div>

                <!-- Mini Sobre -->
                <div class="p-4 bg-light">
                    <h6 class="fw-bold text-dark mb-2" id="preview-sobre-titulo">—</h6>
                    <p class="text-muted small mb-3" id="preview-sobre-texto">—</p>
                    <ul class="list-unstyled small mb-0" id="preview-sobre-topicos"></ul>
                </div>

                <!-- Mini Serviços -->
                <div class="p-4 bg-white border-top">
                    <h6 class="fw-bold text-dark mb-1" id="preview-servicos-titulo">—</h6>
                    <p class="text-muted small mb-3" id="preview-servicos-subtitulo">—</p>
                    <div class="row g-2" id="preview-servicos-cards">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="col-6">
                            <div class="border rounded-3 p-2">
                                <p class="fw-bold small mb-1 text-dark" id="preview-servico<?= $i ?>-titulo">—</p>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;" id="preview-servico<?= $i ?>-descricao">—</p>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Mini Funcionalidades -->
                <div class="p-4 bg-light border-top">
                    <h6 class="fw-bold text-dark mb-1" id="preview-funcionalidades-titulo">—</h6>
                    <p class="text-muted small mb-3" id="preview-funcionalidades-subtitulo">—</p>
                    <ul class="list-unstyled mb-0" id="preview-func-lista">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                        <li class="mb-1 small" id="preview-func<?= $i ?>">
                            <i class="fa-regular fa-circle-check text-brand me-1"></i>—
                        </li>
                        <?php endfor; ?>
                    </ul>
                </div>

                <!-- Mini Contactos -->
                <div class="p-4 border-top">
                    <h6 class="fw-bold text-dark mb-2" id="preview-contactos-titulo">—</h6>
                    <p class="text-muted small mb-2" id="preview-contactos-texto">—</p>
                    <p class="small mb-1"><i class="fa-solid fa-location-dot text-brand me-2"></i><span id="preview-contactos-morada">—</span></p>
                    <p class="small mb-1"><i class="fa-solid fa-phone text-brand me-2"></i><span id="preview-contactos-telefone">—</span></p>
                    <p class="small mb-0"><i class="fa-solid fa-envelope text-brand me-2"></i><span id="preview-contactos-email">—</span></p>
                </div>

                <!-- Mini Rodapé -->
                <div class="p-4 border-top bg-dark text-white">
                    <p class="text-white-50 small mb-3" id="preview-rodape-texto">—</p>
                    <div class="d-flex gap-2 mb-3">
                        <a id="preview-rodape-linkedin" href="#" class="btn btn-sm bg-white bg-opacity-10 text-white rounded-circle" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-linkedin-in" style="font-size:0.75rem;"></i></a>
                        <a id="preview-rodape-github" href="#" class="btn btn-sm bg-white bg-opacity-10 text-white rounded-circle" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-github" style="font-size:0.75rem;"></i></a>
                        <a id="preview-rodape-twitter" href="#" class="btn btn-sm bg-white bg-opacity-10 text-white rounded-circle" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-twitter" style="font-size:0.75rem;"></i></a>
                    </div>
                    <p class="text-white-50 small mb-0" id="preview-rodape-copyright">—</p>
                </div>

            </div>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mapa: name do input -> id do elemento no preview
    const mapaPreview = {
        'hero_titulo':           'preview-hero-titulo',
        'hero_subtitulo':        'preview-hero-subtitulo',
        'metrica1_valor':        'preview-metrica1-valor',
        'metrica1_label':        'preview-metrica1-label',
        'metrica2_valor':        'preview-metrica2-valor',
        'metrica2_label':        'preview-metrica2-label',
        'metrica3_valor':        'preview-metrica3-valor',
        'metrica3_label':        'preview-metrica3-label',
        'metrica4_valor':        'preview-metrica4-valor',
        'metrica4_label':        'preview-metrica4-label',
        'sobre_titulo':          'preview-sobre-titulo',
        'sobre_texto':           'preview-sobre-texto',
        'servicos_titulo':       'preview-servicos-titulo',
        'servicos_subtitulo':    'preview-servicos-subtitulo',
        'servico1_titulo':       'preview-servico1-titulo',
        'servico1_descricao':    'preview-servico1-descricao',
        'servico2_titulo':       'preview-servico2-titulo',
        'servico2_descricao':    'preview-servico2-descricao',
        'servico3_titulo':       'preview-servico3-titulo',
        'servico3_descricao':    'preview-servico3-descricao',
        'servico4_titulo':       'preview-servico4-titulo',
        'servico4_descricao':    'preview-servico4-descricao',
        'servico5_titulo':       'preview-servico5-titulo',
        'servico5_descricao':    'preview-servico5-descricao',
        'servico6_titulo':       'preview-servico6-titulo',
        'servico6_descricao':    'preview-servico6-descricao',
        'funcionalidades_titulo':    'preview-funcionalidades-titulo',
        'funcionalidades_subtitulo': 'preview-funcionalidades-subtitulo',
        'contactos_titulo':      'preview-contactos-titulo',
        'contactos_texto':       'preview-contactos-texto',
        'contactos_morada':      'preview-contactos-morada',
        'contactos_telefone':    'preview-contactos-telefone',
        'contactos_email':       'preview-contactos-email',
        'rodape_texto':          'preview-rodape-texto',
        'rodape_copyright':      'preview-rodape-copyright'
    };

    function atualizarPreview() {
        for (const [nomeCampo, idPreview] of Object.entries(mapaPreview)) {
            const campo = document.querySelector(`[name="${nomeCampo}"]`);
            const alvo  = document.getElementById(idPreview);
            if (campo && alvo) alvo.textContent = campo.value || '—';
        }

        // Tópicos (lista separada por vírgulas)
        const campoTopicos = document.querySelector('[name="sobre_topicos"]');
        const listaTopicos = document.getElementById('preview-sobre-topicos');
        if (campoTopicos && listaTopicos) {
            const topicos = campoTopicos.value.split(',').map(t => t.trim()).filter(Boolean);
            listaTopicos.innerHTML = topicos.map(t =>
                `<li class="mb-1"><i class="fa-regular fa-circle-check text-brand me-2"></i>${t}</li>`
            ).join('');
        }
    }

    // Links do rodapé (actualiza href em vez de textContent)
        ['linkedin', 'github', 'twitter'].forEach(rede => {
            const campo = document.querySelector(`[name="rodape_${rede}"]`);
            const link  = document.getElementById(`preview-rodape-${rede}`);
            if (campo && link) link.href = campo.value || '#';
        });

        // Funcionalidades (8 itens individuais)
        for (let i = 1; i <= 8; i++) {
            const campoFunc = document.querySelector(`[name="func${i}"]`);
            const liFunc    = document.getElementById(`preview-func${i}`);
            if (campoFunc && liFunc) {
                liFunc.innerHTML = `<i class="fa-regular fa-circle-check text-brand me-1"></i>${campoFunc.value || '—'}`;
            }
        }

        // Liga o evento 'input' a todos os campos mapeados + tópicos
        const funcNomes = Array.from({length: 8}, (_, i) => `func${i+1}`);
        const nomesParaOuvir = [...Object.keys(mapaPreview), 'sobre_topicos', ...funcNomes];
    nomesParaOuvir.forEach(nome => {
        const campo = document.querySelector(`[name="${nome}"]`);
        if (campo) campo.addEventListener('input', atualizarPreview);
    });

    // Preencher o preview com os valores actuais assim que a página carrega
    atualizarPreview();
});
</script>

<?php include '../../includes/footer.php'; ?>