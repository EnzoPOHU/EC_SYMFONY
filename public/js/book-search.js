/**
 * Gestion de la recherche et du filtrage des livres
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de recherche de livres chargé');
    
    // Fonction de recherche générique
    function performSearch(searchInput) {
        console.group('Débogage de la recherche');
        console.log('Champ de recherche:', searchInput);
        console.log('Attribut data-datatable-search:', searchInput.getAttribute('data-datatable-search'));
        
        // Trouver le tableau dans le même conteneur que le champ de recherche
        const parentContainer = searchInput.closest('.card');
        console.log('Conteneur parent trouvé:', !!parentContainer);
        
        if (!parentContainer) {
            console.error('Aucun conteneur parent trouvé');
            console.groupEnd();
            return;
        }
        
        // Afficher tous les tableaux dans le conteneur parent
        const allTablesInContainer = parentContainer.querySelectorAll('table');
        console.log('Nombre total de tableaux dans le conteneur:', allTablesInContainer.length);
        allTablesInContainer.forEach((table, index) => {
            console.log(`Table ${index}:`, table);
            console.log(`Table ${index} - data-datatable-table:`, table.getAttribute('data-datatable-table'));
        });
        
        const table = parentContainer.querySelector('table[data-datatable-table="true"]');
        
        if (!table) {
            console.error('Tableau avec data-datatable-table="true" non trouvé');
            console.groupEnd();
            return;
        }
        
        console.log('Tableau trouvé:', table);
        
        const searchTerm = searchInput.value.toLowerCase().trim();
        console.log('Terme de recherche:', searchTerm);
        
        const rows = table.querySelectorAll('tbody tr');
        console.log('Nombre de lignes:', rows.length);
        
        let visibleRowsCount = 0;
        
        rows.forEach((row, index) => {
            // Ignorer la ligne "Aucune lecture"
            if (row.querySelector('td[colspan]')) {
                row.style.display = '';
                return;
            }
            
            const bookNameLink = row.querySelector('.book-name-link');
            const bookCategoryCell = row.querySelector('td:nth-child(2)');
            
            const bookName = bookNameLink ? bookNameLink.textContent.toLowerCase().trim() : '';
            const bookCategory = bookCategoryCell ? bookCategoryCell.textContent.toLowerCase().trim() : '';
            
            console.log(`Ligne ${index} - Livre: ${bookName}, Catégorie: ${bookCategory}`);
            
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
    
    // Trouver tous les champs de recherche
    const searchInputs = document.querySelectorAll('[data-datatable-search]');
    console.log('Nombre de champs de recherche:', searchInputs.length);
    
    // Ajouter des écouteurs d'événements
    searchInputs.forEach(searchInput => {
        console.log('Configuration du champ de recherche:', searchInput);
        searchInput.addEventListener('input', function() {
            performSearch(this);
        });
    });
});
