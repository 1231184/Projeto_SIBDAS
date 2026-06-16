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
        
        // Configuração da Tabela e Paginação (Ficha 11)
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
                zeroRecords: "Nenhum equipamento encontrado com estes filtros.",
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

        // Ligar barra de pesquisa TUA ao DataTables
        $('#inputPesquisa').on('keyup', function() {
            window.tabelaEquipamentos.search(this.value).draw();
        });

        // Filtro avançado das tuas Checkboxes (Motor DataTables)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabelaDados') return true;

            var tr = settings.aoData[dataIndex].nTr;
            if (!tr) return true;

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
        });

        // Atualiza a tabela quando clicas nas checkboxes
        $('.filter-check input[type="checkbox"]').on('change', function() {
            window.tabelaEquipamentos.draw();
        });
    }
});