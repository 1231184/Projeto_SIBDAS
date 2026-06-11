<?php include '../includes/header.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <!-- MAIN -->
    <main class="flex-grow-1 overflow-auto p-4 p-md-5 bg-backend">

        <header class="d-md-none d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="../../assets/img/logotipo.png" alt="MedStock Logo" style="height: 45px; width: auto;">
            </div>
            <button class="btn btn-light border-0 shadow-sm"><i class="fa-solid fa-bars"></i></button>
        </header>

        <div class="mb-4">
            <h1 class="h2 fw-bold text-dark mb-1">Dashboard</h1>
            <p class="text-muted mb-0 fs-6">Visão geral e indicadores de síntese do parque tecnológico hospitalar.</p>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Inventário Global</p>
                            <h2 class="fw-bold text-dark mb-0 fs-1">1,500</h2>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-center mt-3 pt-3 border-top">
                        <div>
                            <div class="text-success fw-bold small">1,410</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Ativos</div>
                        </div>
                        <div>
                            <div class="text-warning text-darken fw-bold small">65</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Manutenção</div>
                        </div>
                        <div>
                            <div class="text-secondary fw-bold small">25</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Inativos</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4 border-bottom border-3 border-danger">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Suporte de Vida</p>
                            <h2 class="fw-bold text-danger mb-0 fs-1">215</h2>
                        </div>
                        <div class="rounded-4 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-heart-pulse fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-dark small fw-medium">Equipamentos Críticos Ativos</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Garantias e Contratos</p>
                            <h2 class="fw-bold text-dark mb-0 fs-1">20</h2>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-center mt-3 pt-3 border-top">
                        <div>
                            <div class="text-danger fw-bold small">12</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Expiradas</div>
                        </div>
                        <div>
                            <div class="text-warning text-darken fw-bold small">8</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">A Expirar (30d)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card dash-card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Documentação em Falta</p>
                            <h2 class="fw-bold text-info text-darken mb-0 fs-1">28</h2>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-muted small fw-medium">Registos incompletos</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-xl-5 col-lg-6">
                <div class="card dash-card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold text-dark mb-1">Equipamentos por Categoria</h5>
                    <p class="text-muted small mb-4">Distribuição proporcional por tipo funcional</p>
                    <div class="position-relative w-100 m-auto" style="height: 250px;">
                        <canvas id="categoriaChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6">
                <div class="card dash-card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Alertas de Gestão</h5>
                            <p class="text-muted small mb-0">Ações e inconformidades que requerem atenção</p>
                        </div>
                        <span class="badge bg-danger rounded-pill">4 Ações</span>
                    </div>
                    
                    <div class="d-flex flex-column gap-2 overflow-auto pe-2" style="max-height: 250px;">
                        
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark small">Ventilador Pulmonar Evita V500</h6>
                                    <p class="text-muted mb-0 font-monospace" style="font-size: 0.75rem;">EQ-0042</p>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">Em Quarentena</span>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark small">Monitor Multiparamétrico IntelliVue</h6>
                                    <p class="text-muted mb-0 font-monospace" style="font-size: 0.75rem;">EQ-2024-001</p>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle text-darken px-2 py-1">Documentação em Falta</span>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark small">Bomba de Infusão Infusomat Space</h6>
                                    <p class="text-muted mb-0 font-monospace" style="font-size: 0.75rem;">EQ-0010</p>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">Garantia Expirada</span>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark small">Ecógrafo GE Healthcare</h6>
                                    <p class="text-muted mb-0 font-monospace" style="font-size: 0.75rem;">EQ-0105</p>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle text-darken px-2 py-1">Contrato a Expirar</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 pb-4">
            <div class="col-12">
                <div class="card dash-card border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-dark mb-1">Equipamentos por Localização</h5>
                    <p class="text-muted small mb-4">Distribuição por serviço hospitalar (A base a vermelho indica a proporção de Suporte de Vida)</p>
                    
                    <div style="height: 350px; width: 100%; position: relative;">
                        <canvas id="servicosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    
    // Capturar os elementos canvas
    const canvasCategoria = document.getElementById('categoriaChart');
    const canvasServicos = document.getElementById('servicosChart');

    if (canvasCategoria && canvasServicos) {
        
        // 1. GRÁFICO CIRCULAR - Equipamentos por Categoria
        const ctxCategoria = canvasCategoria.getContext('2d');
        new Chart(ctxCategoria, {
            type: 'doughnut',
            data: {
                labels: ['Monitorização', 'Terapia/Suporte', 'Diagnóstico', 'Laboratório'],
                datasets: [{
                    data: [450, 320, 185, 90],
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { boxWidth: 12, padding: 15, font: { size: 11, family: "'Inter', sans-serif" } } 
                    }
                },
                cutout: '60%'
            }
        });

        // 2. GRÁFICO DE BARRAS EMPILHADAS (Com barras mais elegantes/finas)
        const ctxServicos = canvasServicos.getContext('2d');
        new Chart(ctxServicos, {
            type: 'bar',
            data: {
                labels: ['UCI', 'Bloco Operatório', 'Serviço de Urgência', 'Enfermaria Geral', 'Imagiologia', 'Laboratório'],
                datasets: [
                    {
                        label: 'Suporte de Vida',
                        data: [112, 68, 30, 0, 0, 5],
                        backgroundColor: '#dc3545',
                        stack: 'ServicosStack',
                        maxBarThickness: 45 // Limita a largura máxima da barra!
                    },
                    {
                        label: 'Outros Equipamentos',
                        data: [168, 127, 120, 300, 120, 85],
                        backgroundColor: '#4169a1',
                        stack: 'ServicosStack',
                        borderRadius: { topLeft: 6, topRight: 6 },
                        maxBarThickness: 45 // Limita a largura máxima da barra!
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { 
                        stacked: true, 
                        grid: { display: false },
                        ticks: { font: { size: 11, family: "'Inter', sans-serif" } }
                    },
                    y: { 
                        stacked: true, 
                        beginAtZero: true, 
                        grid: { borderDash: [4, 4], color: '#e9ecef' },
                        ticks: { font: { size: 11, family: "'Inter', sans-serif" } }
                    }
                },
                plugins: {
                    legend: { 
                        position: 'top', 
                        labels: { boxWidth: 12, padding: 15, font: { size: 11, family: "'Inter', sans-serif" } } 
                    }
                }
            }
        });
    }
});
    </script>


<?php include '../includes/footer.php'; ?>