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
function libererTable(tableId) {
    if (confirm("Êtes-vous sûr de vouloir libérer cette table ?")) {
        fetch("bar.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "table_id=" + encodeURIComponent(tableId)
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Vérifie la réponse du serveur
            location.reload(); // Recharge la page pour mettre à jour l'affichage
        })
        .catch(error => console.error("Erreur:", error));
    }
}
