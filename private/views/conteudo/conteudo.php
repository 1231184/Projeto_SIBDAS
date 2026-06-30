<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

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
            'servicos_titulo', 'servicos_subtitulo', 'funcionalidades_titulo', 'funcionalidades_subtitulo',
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

        <div class="d-flex align-items-center gap-3 mb-4" style="max-width: 1024px; margin: 0 auto;">
            <div>
                <h1 class="h3 fw-bold text-dark mb-0">Gestão de Conteúdo</h1>
                <p class="text-muted small mt-1 mb-0">Edite as informações apresentadas na área pública do website institucional.</p>
            </div>
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
                        <i class="fa-solid fa-layer-group text-brand me-2"></i>Secção: Serviços & Funcionalidades
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1 text-muted">Título "O que oferecemos"</label>
                            <input type="text" name="servicos_titulo" class="form-control shadow-sm bg-white" value="<?= c('servicos_titulo', $rows) ?>">
                            <input type="text" name="servicos_subtitulo" class="form-control shadow-sm bg-white mt-2" value="<?= c('servicos_subtitulo', $rows) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1 text-muted">Título "Funcionalidades"</label>
                            <input type="text" name="funcionalidades_titulo" class="form-control shadow-sm bg-white" value="<?= c('funcionalidades_titulo', $rows) ?>">
                            <input type="text" name="funcionalidades_subtitulo" class="form-control shadow-sm bg-white mt-2" value="<?= c('funcionalidades_subtitulo', $rows) ?>">
                        </div>
                        <div class="col-12 mt-3">
                            <div class="alert alert-secondary small border-0 mb-0 py-2">
                                <i class="fa-solid fa-circle-info me-1"></i> A edição individual dos cartões de serviços e funcionalidades requer intervenção técnica direta na base de dados ou ficheiros do sistema.
                            </div>
                        </div>
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
                <button type="button" class="btn btn-light border px-4 hover-elevate">Cancelar</button>
                <button type="submit" class="btn btn-brand d-inline-flex align-items-center gap-2 px-4 shadow-sm fw-bold">
                    <i class="fa-solid fa-save"></i> Guardar Alterações
                </button>
            </div>

        </form>
    </main>

<?php include '../../includes/footer.php'; ?>