/* ============================================================
    FRONTEND
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    console.log("MedStock Frontend inicializado com sucesso.");

    // Efeito para adicionar sombra extra à Navbar quando se faz scroll
    const navbar = document.querySelector('.navbar');

    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                navbar.classList.add('shadow');
                navbar.classList.remove('shadow-sm');
            } else {
                navbar.classList.remove('shadow');
                navbar.classList.add('shadow-sm');
            }
        });
    }

    // Código futuro para validação de formulários, tooltips ou outras lógicas
    // da "área pública" será colocado aqui.
});