<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedStock</title>

    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../assets/css/1231184.css">
</head>

<body class="bg-login-page d-flex flex-column align-items-center justify-content-center min-vh-100 p-3">

    <div class="w-100" style="max-width: 384px;">

        <div class="mb-4">
            <a href="../public/index.html"
                class="text-white-50 text-decoration-none small d-inline-flex align-items-center gap-2 hover-text-white transition-all">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao início
            </a>
        </div>

        <div class="text-center mb-4">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <div class="bg-brand-subtle d-flex align-items-center justify-content-center rounded-3"
                    style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-stethoscope text-white fs-4"></i>
                </div>
                <h1 class="text-white fw-bold mb-0 fs-2" style="letter-spacing: -1px;">MedStock</h1>
            </div>
            <p class="text-white-50 small mb-0">Sistema de Gestão de Inventário Hospitalar</p>
        </div>

        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4 p-sm-4">

                <div class="mb-4">
                    <h4 class="fw-semibold text-dark mb-1 fs-5">Acesso Reservado</h4>
                    <p class="text-muted small mb-0">Introduza as suas credenciais para continuar</p>
                </div>

                <form action="../private/dashboard/dashboard.html" method="get">

                    <div class="mb-3">
                        <label class="form-label fw-medium text-dark small mb-2">Utilizador *</label>
                        <input type="text" class="form-control px-3 py-2 shadow-sm rounded-2" placeholder="utilizador"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-dark small mb-2">Palavra-passe *</label>
                        <div class="input-group shadow-sm rounded-2">
                            <input type="password" id="passwordInput" class="form-control px-3 py-2 border-end-0"
                                placeholder="••••••••" required>
                            <button type="button" id="togglePassword"
                                class="input-group-text bg-white border-start-0 text-muted">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 rounded-2 py-2 fw-medium mt-1">
                        Entrar
                    </button>

                </form>

                <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.75rem;">
                    Acesso restrito a pessoal autorizado
                </p>

            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-white-50 mb-0" style="font-size: 0.75rem;">MedStock Solutions © 2026</p>
        </div>

    </div>

    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
        });
    </script>
</body>

</html>