document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche la soumission classique du formulaire

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const message = document.getElementById('message');

    // Identifiants de test
    const userTest = 'admin';
    const passTest = 'admin';

    if(username === userTest && password === passTest) {
        message.style.color = 'green';
        message.textContent = 'Connexion réussie !';

        // Redirection après un délai
        setTimeout(() => {
            window.location.href = 'bar.php';
        }, 1500);
    } else {
        message.style.color = 'red';
        message.textContent = 'Identifiants incorrects.';
    }
});
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".close").forEach(button => {
        button.addEventListener("click", function() {
            const tableId = this.dataset.tableId;

            fetch('bar.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `table_id=${tableId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Commande archivée avec succès !");
                    this.closest(".commande-container").remove(); // Enlève la commande de l'écran
                } else {
                    alert("Erreur lors de l'archivage.");
                }
            })
            .catch(error => console.error("Erreur fetch:", error));
        });
    });
});

