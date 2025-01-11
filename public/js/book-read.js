/**
 * Gestion des interactions avec les livres lus
 * 
 * Ce script gère l'ouverture des modales, la mise à jour des livres,
 * et le chargement dynamique des livres de l'utilisateur.
 */

/**
 * Ferme la modal de livre
 * 
 * Masque la modal et réinitialise le formulaire
 */
function closeModal() {
    const modal = document.querySelector('#book_modal');
    modal.classList.remove('show');
    // Réinitialiser le formulaire
    document.querySelector('#book_modal form').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script book-read.js chargé');

    /**
     * Initialise les liens de livres pour la navigation
     * 
     * Ajoute des écouteurs d'événements sur les liens de noms de livres
     * pour permettre l'ouverture des modales de détails
     */
    function initBookNameLinks() {
        const bookNameLinks = document.querySelectorAll('.book-name-link');
        
        console.log('Initialisation des liens de livre. Nombre de liens:', bookNameLinks.length);

        bookNameLinks.forEach(link => {
            // Supprimer les écouteurs existants pour éviter les doublons
            link.removeEventListener('click', handleBookNameClick);
            link.addEventListener('click', handleBookNameClick);
        });
    }

    /**
     * Gère le clic sur un nom de livre
     * 
     * Ouvre la modal avec les détails du livre sélectionné
     * 
     * @param {Event} e - Événement de clic
     */
    function handleBookNameClick(e) {
        e.preventDefault();
        const bookId = this.getAttribute('data-book-id');
        console.log('Lien de livre cliqué. ID:', bookId);
        openBookModal(bookId);
    }

    /**
     * Ouvre la modal de détails d'un livre
     * 
     * Récupère et affiche les informations du livre sélectionné
     * 
     * @param {number} bookId - Identifiant du livre
     */
    function openBookModal(bookId) {
        console.log('Tentative d\'ouverture de la modal pour le livre:', bookId);
        
        const modal = document.querySelector('#book_modal');
        const modalToggle = document.querySelector('[data-modal-toggle="#book_modal"]');
        
        console.log('Éléments trouvés:', {
            modal: !!modal,
            modalToggle: !!modalToggle
        });

        if (modalToggle) {
            console.log('Clic sur le bouton de toggle');
            modalToggle.click();
        } else if (modal) {
            console.log('Affichage direct de la modal');
            modal.style.display = 'block';
            modal.classList.add('show');
        }

        // Initialiser KTModal si disponible
        if (window.KTModal && modal) {
            console.log('Utilisation de KTModal');
            const modalInstance = KTModal.getInstance(modal);
            if (modalInstance) {
                modalInstance.show();
            }
        }

        // Récupérer les détails du livre via une requête fetch
        fetch(`/book-read/${bookId}/details`)
            .then(response => response.json())
            .then(data => {
                // Remplir le formulaire avec les détails du livre
                document.querySelector('#book_modal [name="book_read_id"]').value = data.id;
                document.querySelector('#book_modal [name="book_name"]').value = data.book.name;
                document.querySelector('#book_modal [name="book_category"]').value = data.book.category;
                document.querySelector('#book_modal [name="book_description"]').value = data.description || '';
                document.querySelector('#book_modal [name="book_rating"]').value = data.rating || 1;
                
                // Cocher/décocher la case "Lu"
                const isReadCheckbox = document.querySelector('#book_modal [name="book_is_read"]');
                if (isReadCheckbox) {
                    isReadCheckbox.checked = data.isRead;
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des détails du livre:', error);
                alert('Impossible de charger les détails du livre');
            });
    }

    /**
     * Gère la fermeture du modal
     * 
     * Ajoute des écouteurs sur les boutons de fermeture
     */
    document.querySelectorAll('[data-modal-close="true"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
            }
        });
    });

    // Initialiser les liens de livres
    initBookNameLinks();

    /**
     * Crée un élément HTML pour un livre
     * 
     * @param {Object} bookRead - Données du livre lu
     * @param {boolean} isFinished - Indique si le livre est terminé
     * @returns {HTMLElement} Élément HTML représentant le livre
     */
    function createBookElement(bookRead, isFinished) {
        const bookDiv = document.createElement('div');
        bookDiv.classList.add('book-item');
        
        bookDiv.innerHTML = `
            <h3><a href="#" class="book-name-link" data-book-id="${bookRead.book.id}" data-book-read-id="${bookRead.id}">${bookRead.book.name}</a></h3>
            <p>Catégorie: ${bookRead.book.categoryName}</p>
            <p>Description: ${bookRead.description || 'Pas de description'}</p>
            <p>Note: ${bookRead.rating || 'Non noté'}</p>
        `;

        return bookDiv;
    }

    /**
     * Charge dynamiquement la liste des livres
     * 
     * Récupère les livres en cours et terminés de l'utilisateur
     */
    function fetchBooks() {
        fetch('/books/list')
            .then(response => response.json())
            .then(data => {
                const booksInProgressContainer = document.getElementById('books-in-progress');
                const booksFinishedContainer = document.getElementById('books-finished');

                if (booksInProgressContainer) {
                    booksInProgressContainer.innerHTML = '';
                    data.inProgress.forEach(bookRead => {
                        const bookElement = createBookElement(bookRead, false);
                        booksInProgressContainer.appendChild(bookElement);
                    });
                }

                if (booksFinishedContainer) {
                    booksFinishedContainer.innerHTML = '';
                    data.finished.forEach(bookRead => {
                        const bookElement = createBookElement(bookRead, true);
                        booksFinishedContainer.appendChild(bookElement);
                    });
                }

                // Réinitialiser les liens après avoir chargé les livres
                initBookNameLinks();
            })
            .catch(error => {
                console.error('Erreur lors du chargement des livres:', error);
            });
    }

    // Sélection du formulaire de livre lu
    const form = document.getElementById('book-read-form');

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêche la soumission normale du formulaire

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => {
            // Fermer le modal
            const modal = document.querySelector('#book_modal');
            modal.classList.remove('show');
            
            // Recharger dynamiquement les livres
            fetchBooks();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    });

    // Charger les livres au chargement initial
    fetchBooks();
});
