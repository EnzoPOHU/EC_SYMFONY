/**
 * Gestion de la recherche et du filtrage des livres
 */
document.addEventListener('DOMContentLoaded', function() {
    // Sélection de tous les champs de recherche
    const searchInputs = document.querySelectorAll('[data-datatable-search]');
    
    // Initialisation de la recherche pour chaque tableau
    searchInputs.forEach(searchInput => {
        const tableId = searchInput.getAttribute('data-datatable-search');
        const table = document.querySelector(tableId);
        
        if (table) {
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
    });
});
