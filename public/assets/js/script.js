gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

// Animation du Header (Transparence vers Flou)
gsap.to('.header', {
  scrollTrigger: {
    trigger: 'body',
    start: 'top -20',
    end: 'top -100',
    scrub: true,
  },
  height: '65px',
  paddingLeft: '20px', // On écarte les éléments des bords au scroll
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

function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === "password") {
        input.type = "text";
        icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8a10.03 10.03 0 0 1-2.06.56"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>`;
    } else {
        input.type = "password";
        icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>`;
    }
}

// Message de bienvenue
const welcomeModal = document.getElementById('welcomeDialog');
    
    // Ouvre la modale au chargement de la page
    window.onload = () => {
        welcomeModal.showModal();
    }

// Gestion du Drag and Drop et Validation du Portfolio

const portfolioForm = document.getElementById('portfolioForm');
const publishBtn = document.getElementById('publishBtn');

if (portfolioForm) {
    const zones = document.querySelectorAll('.drop-zone');
    const titleInputs = document.querySelectorAll('.project-title-input');

    // --- FONCTION DE VALIDATION GLOBALE ---
    const updatePublishButton = () => {
        // 1. Vérifie si chaque zone a au moins un fichier (en regardant le contenu de media-list)
        const allProjectsHaveFiles = Array.from(zones).every(zone => {
            return zone.querySelector('.media-list').children.length > 0;
        });

        // 2. Vérifie si tous les titres sont remplis
        const allTitlesFilled = Array.from(titleInputs).every(input => input.value.trim() !== "");

        // Activation du bouton
        publishBtn.disabled = !(allProjectsHaveFiles && allTitlesFilled);
    };

    // --- GESTION DES ZONES DE PROJET ---
    zones.forEach((zone) => {
        const input = zone.querySelector('.drop-zone-input');
        const mediaList = zone.querySelector('.media-list');
        const prompt = zone.querySelector('.drop-zone-prompt');
        let filesCollection = [];

        // Déclencheur de clic (évite le bouton supprimer)
        zone.addEventListener('click', (e) => {
            if (!e.target.classList.contains('btn-remove')) input.click();
        });

        // Traitement des fichiers
        const handleFiles = (files) => {
            Array.from(files).forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    alert(`Le fichier ${file.name} est trop lourd (> 5Mo)`);
                    return;
                }
                if (filesCollection.length < 5) filesCollection.push(file);
            });
            renderMedias();
            updatePublishButton();
        };

        input.addEventListener('change', () => handleFiles(input.files));

        // Drag & Drop visual states
        zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drop-zone--over'); });
        ['dragleave', 'dragend'].forEach(type => {
            zone.addEventListener(type, () => zone.classList.remove('drop-zone--over'));
        });
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('drop-zone--over');
            handleFiles(e.dataTransfer.files);
        });

        // Rendu des miniatures
        function renderMedias() {
            mediaList.innerHTML = '';
            prompt.style.display = filesCollection.length > 0 ? 'none' : 'block';

            filesCollection.forEach((file, index) => {
                const item = document.createElement('div');
                item.classList.add('media-item-mini');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = () => item.style.backgroundImage = `url('${reader.result}')`;
                } else {
                    item.innerHTML = '<span class="video-icon">🎥</span>';
                }

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '×';
                removeBtn.classList.add('btn-remove');
                removeBtn.onclick = (e) => {
                    e.stopPropagation();
                    filesCollection.splice(index, 1);
                    renderMedias();
                    updatePublishButton();
                };
                item.appendChild(removeBtn);
                mediaList.appendChild(item);
            });
        }
    });

    // Écouter la saisie des titres pour valider en temps réel
    titleInputs.forEach(input => {
        input.addEventListener('input', updatePublishButton);
    });
}

// Interface UI : Elle prend deux informations (arguments) : la zone concernée et le fichier sélectionné.

function updateThumbnail(zone, file) {
    let thumbnailElement = zone.querySelector('.drop-zone-thumb'); // Si c'est le premier fichier, elle crée une nouvelle couche visuelle (drop-zone-thumb)
    const prompt = zone.querySelector('.drop-zone-prompt');

    if (prompt) prompt.style.display = 'none';
    // Nettoyage : Elle cache le texte "Glissez un fichier"

    if (!thumbnailElement) {
        thumbnailElement = document.createElement('div');
        thumbnailElement.classList.add('drop-zone-thumb');
        zone.appendChild(thumbnailElement);
    }

    thumbnailElement.dataset.label = file.name;

    if (file.type.startsWith('image/')) {
        const reader = new FileReader(); // FileReader = Objet qui lit le contenu du fichier sur l'ordinateur de l'utilisateur pour en faire une image de fond
        reader.readAsDataURL(file);
        reader.onload = () => {
            thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
            thumbnailElement.innerHTML = '';
        };
    } else if (file.type.startsWith('video/')) {
        thumbnailElement.style.backgroundImage = 'none';
        thumbnailElement.innerHTML = '<div class="video-indicator">Fichier Vidéo</div>';
    }
}


// Gestion des fichiers dans la partie Projet

document.querySelectorAll('.drop-zone').forEach((zone) => {
    // Sélection des éléments internes à la zone actuelle
    const input = zone.querySelector('.drop-zone-input');
    const mediaList = zone.querySelector('.media-list');
    
    let filesCollection = []; // Stocke les fichiers dans le projet précis

    zone.addEventListener('click', (e) => {
        // Sécurité : on n'ouvre pas l'explorateur si l'utilisateur clique sur le bouton "supprimer"
        if (!e.target.classList.contains('btn-remove')) {
            input.click();
        }
    });

    // Gestion de l'ajout (via clic ou drag & drop)
    const handleFiles = (files) => {
        const newFiles = Array.from(files);
        
        newFiles.forEach(file => {
            // Vérification du poids (5 Mo)
            if (file.size > 5 * 1024 * 1024) {
                alert(`Le fichier ${file.name} est trop lourd (> 5Mo)`);
                return;
            }
            // Vérification de la limite (5 fichiers)
            if (filesCollection.length < 5) {
                filesCollection.push(file);
            }
        });

        // Mise à jour de l'affichage et de l'état du bouton de publication
        renderMedias();
        updatePublishButton(); // Active le bouton si les 3 projets sont prêts
    };

    // L'utilisateur choisi ses fichiers via la fenêtre Window/Mac
    input.addEventListener('change', () => handleFiles(input.files));

    // L'utilisateur survole la zone avec un fichier
    zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('drop-zone--over');
    });

    // L'utilisateur quitte la zone sans le déposer
    zone.addEventListener('dragleave', () => zone.classList.remove('drop-zone--over'));

    // L'utilisateur lâche les fichiers dans la zone
    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('drop-zone--over');
        // Récupère les fichiers
        handleFiles(e.dataTransfer.files);
    });

    // Affichage des miniatures et bouton retirer
    function renderMedias() {
        mediaList.innerHTML = '';

        // Gestion du texte d'invite ("Glissez vos fichiers...")
        const prompt = zone.querySelector('.drop-zone-prompt');
        prompt.style.display = filesCollection.length > 0 ? 'none' : 'block';

        // Panier pour créer les éléments HTML
        filesCollection.forEach((file, index) => {
            const item = document.createElement('div');
            item.classList.add('media-item-mini');

            // Si c'est une image, ça génère un aperçu visuel
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => item.style.backgroundImage = `url('${reader.result}')`;
            } else {
                // Sinon, ça génère une icone
                item.innerHTML = '<span class="video-icon">🎥</span>';
            }

            // Création du bouton de suppression
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                filesCollection.splice(index, 1);
                renderMedias();
                updatePublishButton();
            });

            item.appendChild(removeBtn);
            mediaList.appendChild(item);
        });
    }
});

const updatePublishButton = () => {
    const allFilesFilled = Array.from(inputs).every(input => input.files.length > 0);
    const allTitlesFilled = Array.from(document.querySelectorAll('.project-title-input'))
                                 .every(input => input.value.trim() !== "");
    
    publishBtn.disabled = !(allFilesFilled && allTitlesFilled);
};

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

function openEditProjectModal(id, type, title) {
    // Ciblage des champs de la modale
    const inputId = document.getElementById('edit_id_projet');
    const inputType = document.getElementById('edit_type');
    const inputTitle = document.getElementById('edit_title');
    const modal = document.getElementById('editProjectDialog');

    // On vérifie que les éléments existent pour éviter les erreurs JS
    if (inputId && inputType && inputTitle && modal) {
        inputId.value = id;
        inputType.value = type;
        inputTitle.value = title;
        
        // Affichage de la modale
        modal.showModal();
    } else {
        console.error("Erreur : Impossible de trouver les éléments de la modale d'édition.");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const projectCards = document.querySelectorAll('.project-card');
    const slots = document.querySelectorAll('.project-slot');

    projectCards.forEach(card => {
        card.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', card.dataset.id);
            // On stocke aussi le titre pour l'affichage immédiat
            const title = card.querySelector('h4').innerText;
            e.dataTransfer.setData('project-title', title);
        });
    });

    slots.forEach(slot => {
        slot.addEventListener('dragover', (e) => e.preventDefault());

        slot.addEventListener('drop', (e) => {
            e.preventDefault();
            const projectId = e.dataTransfer.getData('text/plain');
            const projectTitle = e.dataTransfer.getData('project-title');
            
            if (projectId) {
                fillSlot(slot, projectId, projectTitle);
            }
        });
    });
});


function fillSlot(slot, id, title) 
{
    const alreadyUsed = document.querySelector(`.project-slot input[value="${id}"]`);
    if (alreadyUsed) {
        alert("Ce projet est déjà sélectionné dans votre assemblage !");
        return;
    }

    // Le HTML sans le onclick
    slot.innerHTML = `
        <div class="selected-content" style="padding: 10px; background: #f0f2f5; border-radius: 8px; width: 100%;">
            <button type="button" class="btn-remove-slot">×</button>
            <strong style="font-size: 0.9em; display: block; margin-bottom: 5px;">Projet Sélectionné :</strong>
            <p style="margin: 0; color: #333;">${title}</p>
            <input type="hidden" name="projets[]" value="${id}">
        </div>
    `;
    slot.classList.add('filled');

    // On ajoute l'écouteur d'événement directement ici !
    const removeBtn = slot.querySelector('.btn-remove-slot');
    removeBtn.addEventListener('click', function() {
        removeProjectFromSlot(this, id);
    });

    const originalCard = document.querySelector(`.project-card[data-id="${id}"]`);
    if (originalCard) {
        originalCard.classList.add('is-used');
    }

    checkFormReady();
}


function removeProjectFromSlot(button, id) {
    const slot = button.closest('.project-slot');
    
    // 1. On remet le slot à son état initial
    slot.innerHTML = '<p>Glissez un projet ici</p>';
    slot.classList.remove('filled');

    // 2. On réactive la carte dans la liste de gauche
    const originalCard = document.querySelector(`.project-card[data-id="${id}"]`);
    if (originalCard) {
        originalCard.classList.remove('is-used');
    }

    checkFormReady();
}

/**
 * Active les boutons de validation uniquement si 3 projets sont présents
 */
function checkFormReady() {
    const filledSlots = document.querySelectorAll('.project-slot.filled').length;
    const publishBtn = document.getElementById('publishBtn');
    const draftBtn = document.querySelector('button[value="draft"]');

    if (filledSlots === 3) {
        publishBtn.disabled = false;
        draftBtn.disabled = false;
    } else {
        publishBtn.disabled = true;
        draftBtn.disabled = true;
    }
}

document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open]');
    const closeBtn = e.target.closest('[data-close]');

    // Si on clique sur un bouton d'ouverture
    if (openBtn) {
        const dialogId = openBtn.getAttribute('data-open');
        const dialog = document.getElementById(dialogId);
        if (dialog) dialog.showModal();
    }

    // Si on clique sur un bouton de fermeture
    if (closeBtn) {
        const dialog = e.target.closest('dialog');
        if (dialog) dialog.close();
    }
});

document.addEventListener('DOMContentLoaded', () => {

    // 1. Gestionnaire universel pour ouvrir/fermer les modales
    document.addEventListener('click', (e) => {
        const openBtn = e.target.closest('[data-open]');
        const closeBtn = e.target.closest('[data-close]');

        if (openBtn) {
            const dialogId = openBtn.getAttribute('data-open');
            const dialog = document.getElementById(dialogId);
            if (dialog) dialog.showModal();
        }

        if (closeBtn) {
            const dialog = e.target.closest('dialog');
            if (dialog) dialog.close();
        }
    });

    // 2. Gestionnaire universel pour les demandes de confirmation (liens et formulaires)
    document.addEventListener('click', (e) => {
        const confirmElement = e.target.closest('a[data-confirm]');
        if (confirmElement) {
            const message = confirmElement.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault(); // Annule le clic si l'utilisateur refuse
            }
        }
    });

    document.addEventListener('submit', (e) => {
        const confirmForm = e.target.closest('form[data-confirm]');
        if (confirmForm) {
            const message = confirmForm.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault(); // Annule l'envoi du formulaire si l'utilisateur refuse
            }
        }
    });

});