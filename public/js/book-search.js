/**
 * Script de gestion de la recherche et du filtrage dynamique des livres
 * 
 * Ce script permet de :
 * - Filtrer dynamiquement les livres dans un tableau
 * - Rechercher des livres par nom ou catégorie
 * - Gérer l'affichage des résultats de recherche
 * - Ouvrir une modal avec les détails d'un livre lors d'un clic
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de recherche de livres chargé');
    
    /**
     * Effectue une recherche dynamique dans un tableau de livres
     * 
     * @param {HTMLElement} searchInput - Le champ de recherche utilisé
     */
    function performSearch(searchInput) {
        // Initialisation du groupe de console pour faciliter le débogage
        console.group('Débogage de la recherche');
        console.log('Champ de recherche:', searchInput);
        console.log('Attribut data-datatable-search:', searchInput.getAttribute('data-datatable-search'));
        
        // Trouver le conteneur parent du tableau pour isoler la zone de recherche
        const parentContainer = searchInput.closest('.card');
        console.log('Conteneur parent trouvé:', !!parentContainer);
        
        // Vérification de l'existence du conteneur parent
        if (!parentContainer) {
            console.error('Aucun conteneur parent trouvé');
            console.groupEnd();
            return;
        }
        
        // Débogage : afficher tous les tableaux dans le conteneur
        const allTablesInContainer = parentContainer.querySelectorAll('table');
        console.log('Nombre total de tableaux dans le conteneur:', allTablesInContainer.length);
        allTablesInContainer.forEach((table, index) => {
            console.log(`Table ${index}:`, table);
            console.log(`Table ${index} - data-datatable-table:`, table.getAttribute('data-datatable-table'));
        });
        
        // Sélectionner le tableau de livres spécifique
        const table = parentContainer.querySelector('table[data-datatable-table="true"]');
        
        // Vérification de l'existence du tableau
        if (!table) {
            console.error('Tableau avec data-datatable-table="true" non trouvé');
            console.groupEnd();
            return;
        }
        
        console.log('Tableau trouvé:', table);
        
        // Normaliser le terme de recherche
        const searchTerm = searchInput.value.toLowerCase().trim();
        console.log('Terme de recherche:', searchTerm);
        
        // Récupérer toutes les lignes du tableau
        const rows = table.querySelectorAll('tbody tr');
        console.log('Nombre de lignes:', rows.length);
        
        // Compteur pour suivre le nombre de lignes visibles
        let visibleRowsCount = 0;
        
        rows.forEach((row, index) => {
            // Conserver la ligne "Aucune lecture" telle quelle
            if (row.querySelector('td[colspan]')) {
                row.style.display = '';
                return;
            }
            
            // Sélectionner les éléments contenant le nom et la catégorie du livre
            const bookNameLink = row.querySelector('.book-name-link');
            const bookCategoryCell = row.querySelector('td:nth-child(2)');
            
            // Extraire et normaliser les informations de recherche
            const bookName = bookNameLink ? bookNameLink.textContent.toLowerCase().trim() : '';
            const bookCategory = bookCategoryCell ? bookCategoryCell.textContent.toLowerCase().trim() : '';
            
            console.log(`Ligne ${index} - Livre: ${bookName}, Catégorie: ${bookCategory}`);
            
            // Filtrer les lignes en fonction du terme de recherche
            if (bookName.includes(searchTerm) || bookCategory.includes(searchTerm)) {
                row.style.display = '';
                visibleRowsCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Gérer l'affichage de la ligne "Aucune lecture"
        const noBookRow = table.querySelector('tr td[colspan]');
        if (noBookRow) {
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            noBookRow.closest('tr').style.display = visibleRows.length === 0 ? '' : 'none';
        }
        
        console.log('Nombre de lignes visibles:', visibleRowsCount);
        console.groupEnd();
    }
    
    // Trouver tous les champs de recherche dans la page
    const searchInputs = document.querySelectorAll('[data-datatable-search]');
    console.log('Nombre de champs de recherche:', searchInputs.length);
    
    // Ajouter des écouteurs d'événements sur chaque champ de recherche
    searchInputs.forEach(searchInput => {
        console.log('Configuration du champ de recherche:', searchInput);
        searchInput.addEventListener('input', function() {
            performSearch(this);
        });
    });

    /**
     * Initialise les liens de livres pour la recherche
     * 
     * Ajoute un gestionnaire de clic sur chaque lien de livre
     * permettant d'ouvrir une modal avec les détails du livre
     */
    function initSearchBookNameLinks() {
        const bookNameLinks = document.querySelectorAll('.book-name-link');
        
        console.log('Initialisation des liens de livre dans la recherche. Nombre de liens:', bookNameLinks.length);

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
     * en utilisant la fonction globale openBookModal
     * 
     * @param {Event} e - Événement de clic
     */
    function handleBookNameClick(e) {
        e.preventDefault();
        const bookId = this.getAttribute('data-book-id');
        console.log('Lien de livre cliqué dans la recherche. ID:', bookId);
        
        // Utiliser la fonction openBookModal du script book-read.js
        if (window.openBookModal) {
            window.openBookModal(bookId);
        } else {
            console.error('La fonction openBookModal n\'est pas disponible');
        }
    }

    // Initialiser les liens de livres au chargement de la page
    initSearchBookNameLinks();
});
