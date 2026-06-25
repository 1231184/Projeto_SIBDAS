/* ============================================================
   MEDSTOCK - JAVASCRIPT PRINCIPAL (FRONTEND)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    console.log("MedStock Frontend iniciado com sucesso.");

    // ==========================================
    // 1. COMPORTAMENTOS GERAIS (Navbar, etc.)
    // ==========================================
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                navbar.classList.add('shadow', 'scrolled');
                navbar.classList.remove('shadow-sm');
            } else {
                navbar.classList.remove('shadow', 'scrolled');
                navbar.classList.add('shadow-sm');
            }
        });
    }

    // ==========================================
    // 2. MOTOR DE FILTROS VISUAIS (Pills e Sidebar)
    // ==========================================
    const sidebar = document.getElementById('filterSidebar');
    const btnToggle = document.getElementById('btnToggleSidebar');
    const txtToggle = document.getElementById('textToggleSidebar');
    const checkboxes = document.querySelectorAll('.filter-check input[type="checkbox"]');
    const activePillsContainer = document.getElementById('activePillsContainer');
    const badgeContador = document.getElementById('badgeContadorFiltros');
    const btnLimpar = document.getElementById('btnLimparFiltros');

    // Função 2.1: Toggle da Barra Lateral
    if (btnToggle) {
        btnToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                txtToggle.innerText = "Mostrar filtros";
                btnToggle.classList.replace('btn-light', 'btn-brand-subtle');
                btnToggle.classList.replace('text-secondary', 'text-brand');
            } else {
                txtToggle.innerText = "Ocultar filtros";
                btnToggle.classList.replace('btn-brand-subtle', 'btn-light');
                btnToggle.classList.replace('text-brand', 'text-secondary');
            }
        });
    }

    // Função 2.2: Atualizar interface visual quando se clica numa checkbox
    function atualizarUI() {
        if (!activePillsContainer) return; // Se não estivermos na página dos equipamentos, ignora
        
        activePillsContainer.innerHTML = '';
        let countAtivos = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                countAtivos++;
                const pill = document.createElement('span');
                pill.className = "badge bg-white border text-secondary d-flex align-items-center gap-2 py-2 px-3 shadow-sm";
                pill.style.fontSize = "0.75rem";
                pill.innerHTML = `${cb.value} <i class="fa-solid fa-xmark cursor-pointer text-danger ms-1" style="font-size:0.9rem;"></i>`;
                
                pill.querySelector('i').addEventListener('click', () => {
                    cb.checked = false;
                    atualizarUI();
                    if(window.tabelaEquipamentos) window.tabelaEquipamentos.draw(); // Avisa o DataTables
                });
                activePillsContainer.appendChild(pill);
            }
        });

        if (countAtivos > 0) {
            badgeContador.innerText = countAtivos;
            badgeContador.classList.remove('d-none');
            btnLimpar.classList.remove('d-none');
        } else {
            badgeContador.classList.add('d-none');
            btnLimpar.classList.add('d-none');
        }
    }

    // Função 2.3: Botão de Limpar todos os filtros
    if (btnLimpar) {
        btnLimpar.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            const inputPesq = document.getElementById('inputPesquisa');
            if(inputPesq) inputPesq.value = '';
            atualizarUI();
            if(window.tabelaEquipamentos) window.tabelaEquipamentos.search('').draw();
        });
    }

    // Ativa os eventos nas checkboxes
    checkboxes.forEach(cb => cb.addEventListener('change', atualizarUI));
});


// ==========================================
// 3. INICIALIZAÇÃO DATATABLES (jQuery)
// ==========================================
$(document).ready(function() {
    if ($('#tabelaDados').length > 0) {
        
        // 1. Configuração Base da Tabela
        window.tabelaEquipamentos = $('#tabelaDados').DataTable({
            pageLength: 5,
            pagingType: "full_numbers",
            dom: 'rt<"d-flex justify-content-between align-items-center mt-3"ip>', 
            language: {
                emptyTable: "Sem dados disponíveis na tabela.",
                info: "A mostrar _START_ até _END_ de _TOTAL_ registos",
                infoEmpty: "A mostrar 0 registos",
                infoFiltered: "(filtrado de _MAX_ totais)",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                zeroRecords: "Nenhum registo encontrado com estes filtros.",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Seguinte",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": classificar ordem crescente",
                    sortDescending: ": classificar ordem decrescente"
                }
            }
        });

        // ----------------------------------------------------
        // LÓGICA ESPECÍFICA: PÁGINA DE EQUIPAMENTOS
        // ----------------------------------------------------
        if (document.getElementById('inputPesquisa')) { // Se a barra de equipamentos existir
            
            // Ligar a barra de pesquisa de Equipamentos
            $('#inputPesquisa').on('keyup', function() {
                window.tabelaEquipamentos.search(this.value).draw();
            });

            // Atualiza a tabela quando clicas nas checkboxes
            $('.filter-check input[type="checkbox"]').on('change', function() {
                window.tabelaEquipamentos.draw();
            });
        }

        // ----------------------------------------------------
        // LÓGICA ESPECÍFICA: PÁGINA DE FORNECEDORES
        // ----------------------------------------------------
        if (document.getElementById('pesquisaFornecedores')) { // Se a barra de fornecedores existir
            
            // Ligar a barra de pesquisa de Fornecedores
            $('#pesquisaFornecedores').on('keyup', function() {
                window.tabelaEquipamentos.search(this.value).draw();
            });

            // Ligar os botões de Filtro Rápido (Fabricante, Distribuidor, etc.)
            $('.btn-filter').on('click', function() {
                $('.btn-filter').removeClass('active');
                $(this).addClass('active');
                window.tabelaEquipamentos.draw();
            });
        }

        // ----------------------------------------------------
        // LÓGICA ESPECÍFICA: PÁGINA DE DOCUMENTAÇÃO
        // ----------------------------------------------------
        if (document.getElementById('pesquisaDocs')) {

            // Ligar a barra de pesquisa
            $('#pesquisaDocs').on('keyup', function() {
                window.tabelaEquipamentos.search(this.value).draw();
            });

            // Ligar os botões de filtro por tipo
            $('.btn-filter').on('click', function() {
                $('.btn-filter').removeClass('active');
                $(this).addClass('active');
                window.tabelaEquipamentos.draw();
            });
        }

        // ----------------------------------------------------
        // LÓGICA ESPECÍFICA: PÁGINA DE GARANTIAS
        // ----------------------------------------------------
        if (document.getElementById('pesquisaGarantias')) {

            $('#pesquisaGarantias').on('keyup', function() {
                window.tabelaEquipamentos.search(this.value).draw();
            });

            $('.btn-filter').on('click', function() {
                $('.btn-filter').removeClass('active');
                $(this).addClass('active');
                window.tabelaEquipamentos.draw();
            });
        }

        // ----------------------------------------------------
        // MOTOR DE FILTRAGEM GLOBAL (Protegido contra erros)
        // ----------------------------------------------------
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabelaDados') return true;

            var tr = settings.aoData[dataIndex].nTr;
            if (!tr) return true;

            // --- Regras para Equipamentos ---
            if (document.getElementById('inputPesquisa')) {
                var estado      = tr.getAttribute('data-estado')      || '';
                var criticidade = tr.getAttribute('data-criticidade') || '';
                var categoria   = tr.getAttribute('data-categoria')   || '';

                var estAtivos  = Array.from(document.querySelectorAll('input[data-group="estado"]:checked')).map(cb => cb.value);
                var critAtivas = Array.from(document.querySelectorAll('input[data-group="criticidade"]:checked')).map(cb => cb.value);
                var catAtivas  = Array.from(document.querySelectorAll('input[data-group="categoria"]:checked')).map(cb => cb.value);
                var servAtivos = Array.from(document.querySelectorAll('input[data-group="servico"]:checked')).map(cb => cb.value);
                var fabAtivos  = Array.from(document.querySelectorAll('input[data-group="fabricante"]:checked')).map(cb => cb.value);

                var matchEstado = estAtivos.length  === 0 || estAtivos.includes(estado);
                var matchCrit   = critAtivas.length === 0 || critAtivas.includes(criticidade);
                var matchCat    = catAtivas.length  === 0 || catAtivas.includes(categoria);
                var matchServ  = servAtivos.length === 0 || servAtivos.includes(tr.getAttribute('data-servico') || '');
                var matchFab   = fabAtivos.length === 0 || fabAtivos.includes(tr.getAttribute('data-fabricante') || '');

                return matchEstado && matchCrit && matchCat && matchServ && matchFab;
            }

            // --- Regras para Fornecedores ---
            if (document.getElementById('pesquisaFornecedores')) {
                var filtroAtivo = $('.btn-filter.active').text().trim();

                if (filtroAtivo === 'Todos' || filtroAtivo === '') return true;

                var celulaHtml = data[1] || '';
                var div = document.createElement('div');
                div.innerHTML = celulaHtml;
                var tipoFornecedor = (div.textContent || div.innerText || '').trim();

                return tipoFornecedor === filtroAtivo;
            }

            // --- Regras para Documentação ---
            if (document.getElementById('pesquisaDocs')) {
                var filtroAtivo = $('.btn-filter.active').attr('data-tipo') || 'Todos';

                if (filtroAtivo === 'Todos' || filtroAtivo === '') return true;

                var tipoCelula = tr.getAttribute('data-tipo') || '';
                return tipoCelula === filtroAtivo;
            }

            // --- Regras para Garantias ---
            if (document.getElementById('pesquisaGarantias')) {
                var filtroAtivo = $('.btn-filter.active').attr('data-tipo') || 'Todos';

                if (filtroAtivo === 'Todos' || filtroAtivo === '') return true;

                var tipoCelula = tr.getAttribute('data-tipo') || '';
                return tipoCelula === filtroAtivo;
            }

            return true;
        });
    }
});

// ==========================================
// 4. LÓGICA AJAX PARA MODAIS (Ver / Editar)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
 
    // ---- Função: preencher o modal "Ver Detalhes" com dados reais ----
    function preencherModalDetalhes(data) {
        const eq   = data.dados;
        const hoje = new Date();
 
        // ---- HEADER ----
        document.getElementById('det-designacao').textContent = eq.designacao || '—';
        document.getElementById('det-codigo').textContent     = eq.codigo_interno || '—';
 
        const badgeEstado = document.getElementById('det-badge-estado');
        const mapaEstado  = { 'Ativo': 'badge-soft-success', 'Em Manutenção': 'badge-soft-warning', 'Em Calibração': 'badge-soft-info', 'Inativo': 'badge-soft-secondary', 'Abatido': 'badge-soft-danger' };
        badgeEstado.className   = 'badge ' + (mapaEstado[eq.estado] || 'badge-soft-secondary');
        badgeEstado.textContent = eq.estado || '—';
 
        const badgeCrit = document.getElementById('det-badge-criticidade');
        const mapaCrit  = { 'Baixa': 'badge-soft-secondary', 'Média': 'badge-soft-info', 'Alta': 'badge-soft-warning', 'Suporte de Vida': 'badge-soft-danger' };
        badgeCrit.className   = 'badge ' + (mapaCrit[eq.criticidade] || 'badge-soft-secondary');
        badgeCrit.textContent = eq.criticidade || '—';
 
        // ---- SEPARADOR GERAL ----
        document.getElementById('det-categoria').textContent    = eq.categoria    || '—';
        document.getElementById('det-marca').textContent        = eq.marca        || '—';
        document.getElementById('det-modelo').textContent       = eq.modelo       || '—';
        document.getElementById('det-serie').textContent        = eq.numero_serie || '—';
        document.getElementById('det-fabricante').textContent   = eq.nome_fabricante || '—';
        document.getElementById('det-ano-fabrico').textContent  = eq.ano_fabrico  || '—';
        document.getElementById('det-tipo-entrada').textContent = eq.tipo_entrada || '—';
        document.getElementById('det-observacoes').textContent  = eq.observacoes  || 'Sem observações.';
 
        const mapaCritLabel = { 'Baixa': 'badge-soft-secondary', 'Média': 'badge-soft-info', 'Alta': 'badge-soft-warning', 'Suporte de Vida': 'badge-soft-danger' };
        document.getElementById('det-criticidade').innerHTML = '<span class="badge ' + (mapaCritLabel[eq.criticidade] || '') + '">' + (eq.criticidade || '—') + '</span>';
 
        const mapaEstadoLabel = { 'Ativo': 'badge-soft-success', 'Em Manutenção': 'badge-soft-warning', 'Em Calibração': 'badge-soft-info', 'Inativo': 'badge-soft-secondary', 'Abatido': 'badge-soft-danger' };
        document.getElementById('det-estado').innerHTML = '<span class="badge ' + (mapaEstadoLabel[eq.estado] || '') + '">' + (eq.estado || '—') + '</span>';
 
        document.getElementById('det-data-aquisicao').textContent = eq.data_aquisicao
            ? new Date(eq.data_aquisicao).toLocaleDateString('pt-PT') : '—';
 
        document.getElementById('det-custo').textContent = eq.custo_aquisicao
            ? parseFloat(eq.custo_aquisicao).toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' }) : '—';
 
        document.getElementById('det-data-registo').textContent = eq.data_registo
            ? new Date(eq.data_registo).toLocaleDateString('pt-PT') : '—';
 
        // ---- SEPARADOR LOCALIZAÇÃO ----
        document.getElementById('det-edificio').textContent = eq.nome_edificio || '—';
        document.getElementById('det-piso').textContent     = eq.nome_piso     || '—';
        document.getElementById('det-servico').textContent  = eq.nome_servico  || '—';
        document.getElementById('det-sala').textContent     = eq.nome_sala     || 'Sem sala atribuída';
 
        const divHist = document.getElementById('det-historico');
        if (data.historico && data.historico.length > 0) {
            divHist.innerHTML = data.historico.map(h => {
                const origem  = h.servico_origem  || 'Entrada Inicial';
                const destino = h.servico_destino || '—';
                const dataMv  = h.data_movimento  ? new Date(h.data_movimento).toLocaleDateString('pt-PT') : '—';
                return `<div class="list-box bg-white border d-flex justify-content-between align-items-center py-2">
                    <div>
                        <span class="fw-bold text-dark small">${h.motivo || '—'}</span>
                        <p class="text-muted small mb-0" style="font-size:0.75rem;">${origem} &rarr; ${destino}</p>
                    </div>
                    <div class="text-end">
                        <p class="text-muted small mb-0" style="font-size:0.75rem;">${dataMv}</p>
                        <p class="text-muted small mb-0" style="font-size:0.65rem;">Por: ${h.utilizador || '—'}</p>
                    </div>
                </div>`;
            }).join('');
        } else {
            divHist.innerHTML = '<p class="text-muted small mb-0">Sem histórico registado.</p>';
        }
 
        // ---- SEPARADOR FORNECEDORES ----
        const divForn = document.getElementById('det-fornecedores');
        document.querySelector('#fornecedores-tab .badge').textContent = data.fornecedores.length;
        if (data.fornecedores && data.fornecedores.length > 0) {
            divForn.innerHTML = data.fornecedores.map(f => `
                <div class="list-box d-flex justify-content-between align-items-center">
                    <div>
                        <p class="detail-value fw-medium mb-0">${f.nome_empresa}</p>
                        <p class="text-muted small mb-0" style="font-size:0.75rem;">${f.papel}</p>
                    </div>
                    <div class="text-end d-none d-sm-block">
                        <p class="text-muted small mb-0" style="font-size:0.75rem;">${f.telefone_geral || '—'}</p>
                        <p class="text-muted small mb-0" style="font-size:0.75rem;">${f.email_geral || '—'}</p>
                    </div>
                </div>`).join('');
        } else {
            divForn.innerHTML = '<p class="text-muted small mb-0">Sem fornecedores associados.</p>';
        }
 
        // ---- SEPARADOR GARANTIAS ----
        const divGar = document.getElementById('det-garantias');
        if (data.garantias && data.garantias.length > 0) {
            divGar.innerHTML = data.garantias.map(g => {
                const dataFim  = new Date(g.data_fim);
                const diffDias = Math.ceil((dataFim - hoje) / (1000 * 60 * 60 * 24));
                let estadoHtml;
                if (diffDias < 0)        estadoHtml = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">Expirada</span>';
                else if (diffDias <= 30)  estadoHtml = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i>A Expirar</span>';
                else                      estadoHtml = '<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">Ativa</span>';
 
                const tipoLabel = g.tipo_cobertura === 'Garantia'
                    ? '<i class="fa-solid fa-shield-halved text-success me-2"></i>Garantia Legal'
                    : '<i class="fa-solid fa-file-contract text-muted me-2"></i>Contrato: ' + (g.tipo_contrato || 'Manutenção');
 
                return `<div class="list-box list-box-light border-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="detail-value fw-medium mb-1">${tipoLabel}</p>
                            ${g.referencia ? '<p class="text-muted small mb-0" style="font-size:0.75rem;">Ref: ' + g.referencia + '</p>' : ''}
                            ${g.entidade_responsavel ? '<p class="text-muted small mb-0" style="font-size:0.75rem;">' + g.entidade_responsavel + '</p>' : ''}
                            <p class="text-muted small mb-0 mt-1" style="font-size:0.75rem;">
                                ${new Date(g.data_inicio).toLocaleDateString('pt-PT')} → ${dataFim.toLocaleDateString('pt-PT')}
                            </p>
                        </div>
                        <div>${estadoHtml}</div>
                    </div>
                </div>`;
            }).join('');
        } else {
            divGar.innerHTML = '<p class="text-muted small mb-0">Sem garantias ou contratos registados.</p>';
        }
 
        // ---- SEPARADOR DOCUMENTOS ----
        const divDoc = document.getElementById('det-documentos');
        document.querySelector('#documentos-tab .badge').textContent = data.documentos.length;
        if (data.documentos && data.documentos.length > 0) {
            divDoc.innerHTML = data.documentos.map(d => {
                let validadeHtml = '';
                if (d.data_validade) {
                    const dataVal  = new Date(d.data_validade);
                    const diffDias = Math.ceil((dataVal - hoje) / (1000 * 60 * 60 * 24));
                    if (diffDias < 0)        validadeHtml = '<p class="text-danger fw-medium small mb-0" style="font-size:0.75rem;"><i class="fa-solid fa-circle-exclamation me-1"></i>Validade: ' + dataVal.toLocaleDateString('pt-PT') + ' (Expirado)</p>';
                    else if (diffDias <= 30)  validadeHtml = '<p class="text-warning fw-medium small mb-0" style="font-size:0.75rem;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Validade: ' + dataVal.toLocaleDateString('pt-PT') + ' (30 dias)</p>';
                    else                      validadeHtml = '<p class="text-muted small mb-0" style="font-size:0.75rem;">Validade: ' + dataVal.toLocaleDateString('pt-PT') + '</p>';
                }
                return `<div class="list-box list-box-light d-flex justify-content-between align-items-center gap-3">
                    <div class="flex-grow-1">
                        <p class="detail-value fw-medium mb-0">${d.titulo || d.caminho_ficheiro}</p>
                        <p class="text-muted small mb-0" style="font-size:0.75rem;">${d.tipo_documento}${d.data_emissao ? ' — ' + new Date(d.data_emissao).toLocaleDateString('pt-PT') : ''}</p>
                        <p class="text-muted custom-monospace small mb-0" style="font-size:0.75rem;"><i class="fa-solid fa-file-pdf text-danger me-1"></i>${d.caminho_ficheiro}</p>
                        ${validadeHtml}
                    </div>
                    <a href="${d.caminho_ficheiro}" download class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0">
                        <i class="fa-solid fa-download"></i><span class="d-none d-sm-inline">Download</span>
                    </a>
                </div>`;
            }).join('');
        } else {
            divDoc.innerHTML = '<p class="text-muted small mb-0">Sem documentos associados.</p>';
        }
 
        // ---- SEPARADOR ACESSÓRIOS ----
        const divAce = document.getElementById('det-acessorios');
        document.querySelector('#acessorios-tab .badge').textContent = data.acessorios.length;
        if (data.acessorios && data.acessorios.length > 0) {
            divAce.innerHTML = data.acessorios.map(a => `
                <div class="list-box list-box-light d-flex justify-content-between align-items-center">
                    <div>
                        <p class="detail-value fw-medium mb-0">${a.designacao}</p>
                        <p class="text-brand small fw-bold mb-0" style="font-size:0.75rem;">${a.codigo_componente || '—'}</p>
                    </div>
                    <div class="text-end">
                        <p class="text-muted custom-monospace small mb-0" style="font-size:0.75rem;">${a.numero_serie || 'Não definido'}</p>
                    </div>
                </div>`).join('');
        } else {
            divAce.innerHTML = '<p class="text-muted small mb-0">Sem acessórios associados.</p>';
        }
    }
 
    // ---- Função: preencher o modal "Editar" com dados reais ----
    function preencherModalEditar(eq) {
        const formEditar = document.getElementById('formEditar');
        if (!formEditar) return;
 
        // Adicionar campo oculto com o ID do equipamento para o UPDATE futuro
        let inputId = formEditar.querySelector('input[name="id_equipamento"]');
        if (!inputId) {
            inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id_equipamento';
            formEditar.appendChild(inputId);
        }
        inputId.value = eq.id_equipamento;
 
        // Preencher os inputs de texto diretos
        formEditar.querySelector('input[name="internalCode"]').value  = eq.codigo_interno;
        formEditar.querySelector('input[name="name"]').value          = eq.designacao;
        formEditar.querySelector('input[name="brand"]').value         = eq.marca;
        formEditar.querySelector('input[name="model"]').value         = eq.modelo;
        formEditar.querySelector('input[name="serialNumber"]').value  = eq.numero_serie;
 
        if (eq.ano_fabrico)     formEditar.querySelector('input[name="manufacturingYear"]').value = eq.ano_fabrico;
        if (eq.data_aquisicao)  formEditar.querySelector('input[name="acquisitionDate"]').value   = eq.data_aquisicao;
        if (eq.custo_aquisicao) formEditar.querySelector('input[name="cost"]').value              = eq.custo_aquisicao;
 
        // Dropdowns nativos (selects)
        formEditar.querySelector('select[name="entryType"]').value = eq.tipo_entrada;
        formEditar.querySelector('select[name="status"]').value    = eq.estado;
 
        // Dropdowns customizados
        if (typeof selecionarDropdownEdit === 'function') {
            selecionarDropdownEdit('Categoria',   eq.categoria);
            selecionarDropdownEdit('Criticidade', eq.criticidade);
        }
 
        // Observações
        const obs = formEditar.querySelector('textarea[name="observations"]');
        if (obs) obs.value = eq.observacoes || '';
    }
 
    // ---- Botões "Ver" na tabela ----
    document.querySelectorAll('.btn-ver-eq').forEach(botao => {
        botao.addEventListener('click', function() {
            const id            = this.getAttribute('data-id');
            const textoOriginal = this.innerHTML;
            const btn           = this;
 
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
 
            fetch('api/get_equipamento.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    btn.innerHTML = textoOriginal;
 
                    if (!data.sucesso) {
                        alert('Erro ao carregar dados: ' + data.erro);
                        return;
                    }
 
                    preencherModalDetalhes(data);
                    preencherModalEditar(data.dados);
                    new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
                })
                .catch(error => {
                    btn.innerHTML = textoOriginal;
                    console.error('Erro no AJAX:', error);
                    alert('Ocorreu um erro de comunicação com a base de dados.');
                });
        });
    });
 
    // ---- Abrir via URL ?abrir=ID (vindo de fornecedores ou localizações) ----
    const params  = new URLSearchParams(window.location.search);
    const idAbrir = params.get('abrir');
 
    if (idAbrir) {
        const botaoAlvo = document.querySelector('.btn-ver-eq[data-id="' + idAbrir + '"]');
 
        if (botaoAlvo) {
            // Se o botão existir na tabela (página não filtrada), clica nele diretamente
            botaoAlvo.click();
        } else {
            // Se não existir (página filtrada por serviço/sala), faz fetch direto
            fetch('api/get_equipamento.php?id=' + idAbrir)
                .then(r => r.json())
                .then(data => {
                    if (!data.sucesso) return;
                    preencherModalDetalhes(data);
                    preencherModalEditar(data.dados);
                    new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
                });
        }
    }
});