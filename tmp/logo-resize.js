// Script pour augmenter la taille des logos
document.addEventListener('DOMContentLoaded', function() {
  // Modification du logo dans la navbar
  const navbarLogo = document.querySelector('.navbar-brand img');
  if (navbarLogo) {
    navbarLogo.style.height = '75px';
    navbarLogo.style.transition = 'all 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
    navbarLogo.style.transformOrigin = 'left center';
  }

  // Mise à jour de la taille lors du défilement
  const updateScrolledLogo = () => {
    const scrolled = document.querySelector('.navbar-scrolled img');
    if (scrolled) {
      scrolled.style.height = '65px';
    }
  };

  // Vérifier si la navbar est déjà scrolled au chargement
  if (document.querySelector('.navbar-scrolled')) {
    updateScrolledLogo();
  }

  // Ajouter un écouteur d'événement pour le défilement
  window.addEventListener('scroll', updateScrolledLogo);

  // Modification du logo dans le footer
  const footerLogo = document.querySelector('.footer-logo img');
  if (footerLogo) {
    footerLogo.style.height = '65px';
    footerLogo.style.transition = 'transform 0.3s ease';
    
    // Ajouter un effet hover
    footerLogo.addEventListener('mouseenter', function() {
      this.style.transform = 'scale(1.05)';
    });
    
    footerLogo.addEventListener('mouseleave', function() {
      this.style.transform = 'scale(1)';
    });
  }
}); 