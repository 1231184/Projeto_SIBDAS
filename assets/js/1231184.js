/*
 * Aluno: 1231184
 * Projeto: MediCore Systems
 */

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. ANIMAÇÕES AO FAZER SCROLL (Intersection Observer)
    // Faz com que as secções e cartões surjam suavemente ao descer a página
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Remove a invisibilidade forçada para a animação do CSS poder atuar
                entry.target.style.opacity = ''; 
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target); // Anima apenas uma vez
            }
        });
    }, observerOptions);

    // Seleciona todos os cartões e títulos principais do front-office para animar
    const animatableElements = document.querySelectorAll('.medicore-card, section h2');
    animatableElements.forEach(el => {
        el.style.opacity = '0'; // Esconde inicialmente
        observer.observe(el);
    });

});