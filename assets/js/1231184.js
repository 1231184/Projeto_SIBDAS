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
        // MOTOR DE FILTRAGEM GLOBAL (Protegido contra erros)
        // ----------------------------------------------------
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabelaDados') return true;

            var tr = settings.aoData[dataIndex].nTr;
            if (!tr) return true;

            // --- Regras para Equipamentos ---
            if (document.getElementById('inputPesquisa')) {
                var estado = tr.getAttribute('data-estado') || '';
                var criticidade = tr.getAttribute('data-criticidade') || '';
                var categoria = tr.getAttribute('data-categoria') || '';

                var estAtivos = Array.from(document.querySelectorAll('input[data-group="estado"]:checked')).map(cb => cb.value);
                var critAtivas = Array.from(document.querySelectorAll('input[data-group="criticidade"]:checked')).map(cb => cb.value);
                var catAtivas = Array.from(document.querySelectorAll('input[data-group="categoria"]:checked')).map(cb => cb.value);

                var matchEstado = estAtivos.length === 0 || estAtivos.includes(estado);
                var matchCrit = critAtivas.length === 0 || critAtivas.includes(criticidade);
                var matchCat = catAtivas.length === 0 || catAtivas.includes(categoria);

                return matchEstado && matchCrit && matchCat;
            }

            // --- Regras para Fornecedores ---
if (document.getElementById('pesquisaFornecedores')) {
    var filtroAtivo = $('.btn-filter.active').text().trim();
    
    if (filtroAtivo === 'Todos' || filtroAtivo === '') {
        return true;
    }
    
    // Lê o texto da célula da coluna Tipo (índice 1) sem HTML
    var celulaHtml = data[1] || "";
    var div = document.createElement('div');
    div.innerHTML = celulaHtml;
    var tipoFornecedor = div.textContent || div.innerText || "";
    
    return tipoFornecedor.trim() === filtroAtivo;
}

            return true;
        });
    }
});

// ==========================================
// 4. LÓGICA AJAX PARA MODAIS (Ver / Editar)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Selecionar todos os botões "Ver" da tabela
    const botoesVer = document.querySelectorAll('.btn-ver-eq');

    botoesVer.forEach(botao => {
        botao.addEventListener('click', function() {
            const idEquipamento = this.getAttribute('data-id');

            // 1. Mostrar um pequeno "loading" no botão para o utilizador perceber que está a carregar
            const textoOriginal = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            // 2. Fazer o pedido AJAX à nossa nova API
            fetch(`api/get_equipamento.php?id=${idEquipamento}`)
                .then(response => response.json())
                .then(data => {
                    // Restaurar o botão ao estado original
                    this.innerHTML = textoOriginal; 

                    if (data.sucesso) {
                        const eq = data.dados;

                        // ==========================================
                        // 3. PREENCHER O MODAL DE EDIÇÃO
                        // ==========================================
                        const formEditar = document.getElementById('formEditar');
                        if (formEditar) {
                            
                            // Adicionar um ID oculto para o UPDATE saber que equipamento atualizar (Passo 4 futuro)
                            let inputId = formEditar.querySelector('input[name="id_equipamento"]');
                            if (!inputId) {
                                inputId = document.createElement('input');
                                inputId.type = 'hidden';
                                inputId.name = 'id_equipamento';
                                formEditar.appendChild(inputId);
                            }
                            inputId.value = eq.id_equipamento;

                            // Preencher os inputs de texto diretos
                            formEditar.querySelector('input[name="internalCode"]').value = eq.codigo_interno;
                            formEditar.querySelector('input[name="name"]').value = eq.designacao;
                            formEditar.querySelector('input[name="brand"]').value = eq.marca;
                            formEditar.querySelector('input[name="model"]').value = eq.modelo;
                            formEditar.querySelector('input[name="serialNumber"]').value = eq.numero_serie;

                            if (eq.ano_fabrico) formEditar.querySelector('input[name="manufacturingYear"]').value = eq.ano_fabrico;
                            if (eq.data_aquisicao) formEditar.querySelector('input[name="acquisitionDate"]').value = eq.data_aquisicao;
                            if (eq.custo_aquisicao) formEditar.querySelector('input[name="cost"]').value = eq.custo_aquisicao;

                            // Dropdowns Nativos (Selects)
                            formEditar.querySelector('select[name="entryType"]').value = eq.tipo_entrada;
                            formEditar.querySelector('select[name="status"]').value = eq.estado;

                            // Dropdowns Customizados (Usando a tua própria função do JavaScript!)
                            if(typeof selecionarDropdownEdit === 'function') {
                                selecionarDropdownEdit('Categoria', eq.categoria);
                                selecionarDropdownEdit('Criticidade', eq.criticidade);
                            }

                            // Observações
                            const obs = formEditar.querySelector('textarea[name="observations"]');
                            if (obs) obs.value = eq.observacoes || '';
                            
                            // (No futuro, preenchemos também o modal "Ver Detalhes" aqui)
                        }

                        // ==========================================
                        // 4. ABRIR O MODAL PROGRAMATICAMENTE
                        // ==========================================
                        const modalDetalhes = new bootstrap.Modal(document.getElementById('modalDetalhes'));
                        modalDetalhes.show();

                    } else {
                        alert("Erro ao carregar dados: " + data.erro);
                    }
                })
                .catch(error => {
                    this.innerHTML = textoOriginal;
                    console.error('Erro no AJAX:', error);
                    alert("Ocorreu um erro de comunicação com a base de dados.");
                });
        });
    });
});

// ==========================================
// FICHA 13: Abrir modal de detalhes via URL
// Usado quando se vem da página de fornecedores
// com o parâmetro ?abrir=ID na URL
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const params    = new URLSearchParams(window.location.search);
    const idAbrir   = params.get('abrir');

    if (idAbrir) {
        const botaoAlvo = document.querySelector('.btn-ver-eq[data-id="' + idAbrir + '"]');

        if (botaoAlvo) {
            botaoAlvo.click();
        } else {
            fetch('api/get_equipamento.php?id=' + idAbrir)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.sucesso) {
                        const eq = data.dados;
                        const formEditar = document.getElementById('formEditar');
                        if (formEditar) {
                            let inputId = formEditar.querySelector('input[name="id_equipamento"]');
                            if (!inputId) {
                                inputId = document.createElement('input');
                                inputId.type = 'hidden';
                                inputId.name = 'id_equipamento';
                                formEditar.appendChild(inputId);
                            }
                            inputId.value = eq.id_equipamento;
                            formEditar.querySelector('input[name="internalCode"]').value = eq.codigo_interno;
                            formEditar.querySelector('input[name="name"]').value = eq.designacao;
                            formEditar.querySelector('input[name="brand"]').value = eq.marca;
                            formEditar.querySelector('input[name="model"]').value = eq.modelo;
                            formEditar.querySelector('input[name="serialNumber"]').value = eq.numero_serie;
                            if (eq.ano_fabrico) formEditar.querySelector('input[name="manufacturingYear"]').value = eq.ano_fabrico;
                            if (eq.data_aquisicao) formEditar.querySelector('input[name="acquisitionDate"]').value = eq.data_aquisicao;
                            if (eq.custo_aquisicao) formEditar.querySelector('input[name="cost"]').value = eq.custo_aquisicao;
                            formEditar.querySelector('select[name="entryType"]').value = eq.tipo_entrada;
                            formEditar.querySelector('select[name="status"]').value = eq.estado;
                            if (typeof selecionarDropdownEdit === 'function') {
                                selecionarDropdownEdit('Categoria', eq.categoria);
                                selecionarDropdownEdit('Criticidade', eq.criticidade);
                            }
                            const obs = formEditar.querySelector('textarea[name="observations"]');
                            if (obs) obs.value = eq.observacoes || '';
                        }
                        new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
                    }
                });
        }
    }
});