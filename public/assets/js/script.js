
// Animation du Header (Transparence vers Flou)
gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
gsap.to('.header', {
  scrollTrigger: {
    trigger: 'body',
    start: 'top -20',
    end: 'top -100',
    scrub: true,
  },
  height: '65px',
  paddingLeft: '20px',
  paddingRight: '20px',
  backgroundColor: 'rgba(255, 255, 255, 0.1)',
  backdropFilter: 'blur(25px)',
});


// Etirement de la barre de recherche
const searchInput = document.querySelector('.header-search input');

if (searchInput) {
    searchInput.addEventListener('focus', () => {
        gsap.to('.header-search', { maxWidth: '450px', duration: 0.4, ease: 'power2.out' });
    });

  searchInput.addEventListener('blur', () => {
    gsap.to('.header-search', { maxWidth: '350px', duration: 0.4, ease: 'power2.in' });
  });
}

// Gestion de la fenêtre modale de mot de passe
const mdpDialog = document.querySelector('#mdpDialog');
const ouvreBtn = document.querySelector('#ouvreDialog');
const fermeBtn = document.querySelector('#fermeDialog');

if (mdpDialog && ouvreBtn && fermeBtn) {
    ouvreBtn.addEventListener('click', () => {
        mdpDialog.showModal();
    });

    fermeBtn.addEventListener('click', () => {
        mdpDialog.close();
    });
}

// Message de bienvenue
const welcomeModal = document.getElementById('welcomeDialog');
    
    // Ouvre la modale au chargement de la page
window.onload = () => {
        welcomeModal.showModal();
    }

// Fonction pour basculer l'affichage du mot de passe
function togglePassword(inputId, svgId) {
    const input = document.getElementById(inputId);
    const svg = document.getElementById(svgId);
    
    if (input.type === "password") {
        input.type = "text";
        // Icône Œil barré (Slash)
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        input.type = "password";
        // Icône Œil ouvert
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}

// Gestion du Menu Burger
const burgerMenu = document.querySelector('.burger-menu');
const nav = document.querySelector('.nav');

if (burgerMenu && nav) {
    burgerMenu.addEventListener('click', () => {
        // Bascule les classes 'active'
        burgerMenu.classList.toggle('active');
        nav.classList.toggle('active');

        // Mise à jour de l'accessibilité
        const isExpanded = burgerMenu.getAttribute('aria-expanded') === 'true';
        burgerMenu.setAttribute('aria-expanded', !isExpanded);
        
        // Empêche le défilement du corps de la page quand le menu est ouvert
        if (!isExpanded) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });

}