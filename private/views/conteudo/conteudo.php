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
                            <input type="text" class="form-control shadow-sm bg-white" value="Gestão Inteligente de Equipamentos Médicos">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Introdutório (Lead)</label>
                            <textarea class="form-control shadow-sm bg-white" rows="2">Plataforma integrada para inventário, documentação e ciclo de vida de equipamento hospitalar. Desenvolvida para apoiar as equipas de engenharia biomédica em hospitais e clínicas de todo o país.</textarea>
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
                            <input type="text" class="form-control shadow-sm bg-white mb-2" value="ISO 13485">
                            <input type="text" class="form-control shadow-sm bg-light" value="Certificação para Eq. Médico">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 2</label>
                            <input type="text" class="form-control shadow-sm bg-white mb-2" value="+50 000">
                            <input type="text" class="form-control shadow-sm bg-light" value="Equipamentos Suportados">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 3</label>
                            <input type="text" class="form-control shadow-sm bg-white mb-2" value="24/7">
                            <input type="text" class="form-control shadow-sm bg-light" value="Suporte Técnico">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-medium mb-1">Métrica 4</label>
                            <input type="text" class="form-control shadow-sm bg-white mb-2" value="RGPD">
                            <input type="text" class="form-control shadow-sm bg-light" value="Proteção de Dados">
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
                            <input type="text" class="form-control shadow-sm bg-white" value="Sobre a MedStock Solutions">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Descritivo</label>
                            <textarea class="form-control shadow-sm bg-white" rows="3">A MedStock Solutions é uma empresa portuguesa especializada no desenvolvimento de soluções de gestão para o setor da saúde. Com mais de 10 anos de experiência, apoiamos hospitais, clínicas e centros de saúde na modernização dos seus processos de gestão de equipamento médico.</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Tópicos de Destaque (Separados por vírgula)</label>
                            <textarea class="form-control shadow-sm bg-white" rows="2">Certificação ISO 13485 para equipamento médico, Conformidade com RGPD e legislação portuguesa, Suporte técnico especializado 24/7, Formação incluída na implementação</textarea>
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
                            <input type="text" class="form-control shadow-sm bg-white" value="O que oferecemos">
                            <input type="text" class="form-control shadow-sm bg-white mt-2" value="Soluções completas para a gestão do ciclo de vida dos equipamentos médicos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1 text-muted">Título "Funcionalidades"</label>
                            <input type="text" class="form-control shadow-sm bg-white" value="Tecnologia ao serviço da saúde">
                            <input type="text" class="form-control shadow-sm bg-white mt-2" value="Desenvolvido especificamente para responder às exigências da engenharia biomédica hospitalar">
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
                            <input type="text" class="form-control shadow-sm bg-white" value="Fale connosco">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium mb-1">Texto Introdutório</label>
                            <textarea class="form-control shadow-sm bg-white" rows="2">Tem alguma dúvida sobre a plataforma MedStock Solutions ou quer agendar uma demonstração para a sua unidade de saúde? O nosso suporte está aqui para o ajudar.</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-medium mb-1">Sede / Morada</label>
                            <input type="text" class="form-control shadow-sm bg-white" value="ISEP - Instituto Superior de Engenharia do Porto, Rua Dr. António Bernardino de Almeida, 431">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1">Telefone (Geral)</label>
                            <input type="text" class="form-control shadow-sm bg-white" value="+351 228 340 500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium mb-1">E-mail de Suporte</label>
                            <input type="email" class="form-control shadow-sm bg-white" value="suporte@medstock.isep.ipp.pt">
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
                            <textarea class="form-control shadow-sm bg-white" rows="2">A plataforma líder para a gestão inteligente do ciclo de vida de equipamentos médicos. Simplifique a sua operação hospitalar connosco.</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium mb-1">Link LinkedIn</label>
                            <input type="url" class="form-control shadow-sm bg-white" placeholder="https://linkedin.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium mb-1">Link GitHub</label>
                            <input type="url" class="form-control shadow-sm bg-white" placeholder="https://github.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium mb-1">Link Twitter / X</label>
                            <input type="url" class="form-control shadow-sm bg-white" placeholder="https://twitter.com/...">
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label small fw-medium mb-1">Texto de Copyright</label>
                            <input type="text" class="form-control shadow-sm bg-white" value="&copy; 2026 MedStock Solutions. Todos os direitos reservados.">
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