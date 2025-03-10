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