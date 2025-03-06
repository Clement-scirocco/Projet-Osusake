<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>
    <div class="commande-details-container">
        <h2>Merci pour votre commande !</h2>
        <p>Voici les détails de votre commande :</p>
        <ul id="commande-liste"></ul>
        <p id="total-prix">Total : 0€</p>
        <button onclick="retourAccueil()" class="accueil-btn">Retour au menu</button>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log("Chargement des données...");

            // ✅ 2️⃣ Récupération des plats commandés
            let commande = JSON.parse(localStorage.getItem("commande")) || [];
            let commandeListe = document.getElementById("commande-liste");
            commandeListe.innerHTML = "";

            let total = 0;

            // ✅ 3️⃣ Affichage des articles commandés et calcul du total
            commande.forEach(plat => {
                let li = document.createElement("li");
                let prixTotal = plat.prix * plat.quantite;
                li.innerText = `${plat.nom} (x${plat.quantite}) - ${prixTotal}€`;
                commandeListe.appendChild(li);
                total += prixTotal;
            });

            // ✅ 4️⃣ Mise à jour du total
            document.getElementById("total-prix").innerText = `Total : ${total.toFixed(2)}€`;
            localStorage.setItem("totalPrix", total.toFixed(2));
        });

        // ✅ 5️⃣ Nettoyage des données et retour au menu
        function retourAccueil() {
            localStorage.removeItem("commande");
            localStorage.removeItem("totalPrix");
            localStorage.removeItem("tableChoisie");
            window.location.href = "menu.php";
        }
    </script>
</body>
</html>
