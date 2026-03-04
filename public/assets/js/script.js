gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

// 1. Animation du Header (Transparence vers Flou)
gsap.to('.header', {
  scrollTrigger: {
    trigger: 'body',
    start: 'top -20',
    end: 'top -100',
    scrub: true,
  },
  height: '60px',
  backgroundColor: 'rgba(255, 255, 255, 0.1)', 
  backdropFilter: 'blur(25px)', 
});

// 2. Animation des textes (Mouvement + Apparition)
const textElements = ['.text0', '.text1', '.text2', '.text3'];

textElements.forEach((selector, index) => {
  const isRight = index % 2 === 0;
  
  gsap.to(selector, {
    x: isRight ? '10%' : '-10%',
    rotate: isRight ? 5 : -5,
    opacity: 1,
    y: 0,
    scrollTrigger: {
      trigger: selector,
      start: 'top 90%', // Apparaît plus tôt
      end: 'top 20%',
      scrub: 1,
    }
  });
});

// 3. Retour en haut
document.querySelector('#back').addEventListener('click', () => {
  gsap.to(window, { duration: 1, scrollTo: 0 });
});

// 4. Etirement de la barre de recherche
const searchInput = document.querySelector('.header-search input');

if (searchInput) {
    searchInput.addEventListener('focus', () => {
        gsap.to('.header-search', { maxWidth: '450px', duration: 0.4, ease: 'power2.out' });
    });

    searchInput.addEventListener('blur', () => {
        gsap.to('.header-search', { maxWidth: '350px', duration: 0.4, ease: 'power2.in' });
    });
}