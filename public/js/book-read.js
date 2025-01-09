function closeModal() {
    const modal = document.querySelector('#book_modal');
    modal.classList.remove('show');
    // Réinitialiser le formulaire
    document.querySelector('#book_modal form').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    // Gérer la fermeture du modal
    document.querySelectorAll('[data-modal-close="true"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            closeModal();
        });
    });

    // Intercepter la soumission du formulaire
    const form = document.querySelector('#book_modal form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Envoyer les données en AJAX
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Recharger la page pour afficher les changements
            window.location.reload();
        });
    });
});
