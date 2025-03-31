let tentatives = 0;
let tentativeAutorisee = true;

document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const message = document.getElementById('message');

    const userTest = 'admin';
    const passTest = 'admin';

    if (!tentativeAutorisee) {
        message.style.color = 'orange';
        message.textContent = 'Veuillez patienter avant de réessayer.';
        return;
    }

    if(username === userTest && password === passTest) {
        message.style.color = 'green';
        message.textContent = 'Connexion réussie !';
        
        setTimeout(() => {
            window.location.href = 'Carte.html';
        }, 1500);
        
    } else {
        tentatives += 1;

        if (tentatives >= 3) {
            message.style.color = 'red';
            message.textContent = 'Trop de tentatives. Réessayez dans 30 secondes.';
            tentativeAutorisee = false;
            setTimeout(() => {
                tentativeAutorisee = true;
                tentatives = 0;
                message.textContent = '';
            }, 30000); // 30 secondes après 3 échecs
        } else {
            message.style.color = 'red';
            message.textContent = `Identifiants incorrects. Réessayez dans 3 secondes. (${tentatives}/3 tentatives)`;
            tentativeAutorisee = false;
            setTimeout(() => {
                tentativeAutorisee = true;
                message.textContent = '';
            }, 3000); // 3 secondes après chaque tentative ratée
        }
    }
});