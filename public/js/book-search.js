/**
 * Gestion de la recherche et du filtrage des livres
 * 
 * Ce script permet de :
 * - Filtrer dynamiquement les livres en cours de lecture
 * - Filtrer dynamiquement les livres terminés
 */
document.addEventListener('DOMContentLoaded', function() {
    // Sélection de tous les champs de recherche
    const searchInputs = document.querySelectorAll('[data-datatable-search]');
    
    /**
     * Initialise la recherche dynamique pour un tableau
     * @param {HTMLInputElement} searchInput - Champ de recherche
     * @param {HTMLTableElement} table - Tableau à filtrer
     */
    function initDynamicSearch(searchInput, table) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                // Récupération des éléments de recherche
                const bookNameLink = row.querySelector('.book-name-link');
                const bookCategoryCell = row.querySelector('td:nth-child(2)');
                
                // Vérification de la correspondance
                const bookName = bookNameLink ? bookNameLink.textContent.toLowerCase() : '';
                const bookCategory = bookCategoryCell ? bookCategoryCell.textContent.toLowerCase() : '';
                
                // Affichage conditionnel de la ligne
                if (bookName.includes(searchTerm) || bookCategory.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Initialisation de la recherche pour chaque tableau
    searchInputs.forEach(searchInput => {
        const tableId = searchInput.getAttribute('data-datatable-search');
        const table = document.querySelector(tableId);
        
        if (table) {
            initDynamicSearch(searchInput, table);
        }
    });

    /**
     * Crée un élément de livre dynamiquement
     * @param {Object} bookRead - Données du livre
     * @param {boolean} isFinished - Indique si le livre est terminé
     * @returns {HTMLElement} Élément de livre créé
     */
    function createBookElement(bookRead, isFinished) {
        const bookDiv = document.createElement('div');
        bookDiv.classList.add('book-item');
        
        bookDiv.innerHTML = `
            <h3>${bookRead.book.name}</h3>
            <p>Catégorie: ${bookRead.book.categoryName}</p>
            <p>Description: ${bookRead.description || 'Pas de description'}</p>
            <p>Note: ${bookRead.rating || 'Non noté'}</p>
        `;

        return bookDiv;
    }

    // Sélection du champ de recherche pour les livres en cours
    const searchInputInProgress = document.querySelector('[data-datatable-search="#in-progress-books-table"]');
    const booksInProgressContainer = document.getElementById('books-in-progress');

    if (!searchInputInProgress) return;

    /**
     * Gère la recherche dynamique des livres en cours
     * 
     * Filtre les livres en fonction du terme de recherche saisi
     */
    searchInputInProgress.addEventListener('input', function() {
        const searchTerm = this.value.trim();

        // Si le terme de recherche est trop court, recharger tous les livres
        if (searchTerm.length < 2) {
            // Recharger tous les livres en cours
            fetchInProgressBooks();
            return;
        }

        // Requête de recherche de livres
        fetch(`/books/search?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                // Réinitialiser le conteneur des livres en cours
                booksInProgressContainer.innerHTML = '';

                // Afficher uniquement les livres en cours filtrés
                data.inProgress.forEach(bookRead => {
                    const bookElement = createBookElement(bookRead, false);
                    booksInProgressContainer.appendChild(bookElement);
                });
            })
            .catch(error => {
                console.error('Erreur lors de la recherche des livres en cours:', error);
                alert('Impossible de rechercher les livres en cours');
            });
    });

    /**
     * Récupère la liste des livres en cours
     * 
     * Charge tous les livres en cours de lecture de l'utilisateur
     */
    function fetchInProgressBooks() {
        fetch('/books/list')
            .then(response => response.json())
            .then(data => {
                // Réinitialiser le conteneur des livres en cours
                booksInProgressContainer.innerHTML = '';

                // Afficher tous les livres en cours
                data.inProgress.forEach(bookRead => {
                    const bookElement = createBookElement(bookRead, false);
                    booksInProgressContainer.appendChild(bookElement);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des livres en cours:', error);
                alert('Impossible de charger les livres en cours');
            });
    }

    // Charger initialement les livres en cours
    fetchInProgressBooks();
});
