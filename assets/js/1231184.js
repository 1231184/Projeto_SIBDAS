/* ============================================================
    FRONTEND
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    console.log("MedStock Frontend iniciado com sucesso.");

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
});