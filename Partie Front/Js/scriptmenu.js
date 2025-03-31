document.addEventListener("DOMContentLoaded", function() {
    console.log("Menu chargé !");

    // Récupérer la table choisie depuis localStorage
    let tableChoisie = localStorage.getItem("tableChoisie");
    document.getElementById("table-numero").textContent = tableChoisie ? tableChoisie : "Aucune";

    // Cacher toutes les descriptions au chargement de la page
    let overlays = document.querySelectorAll('.details-overlay');
    overlays.forEach(overlay => {
        overlay.style.display = 'none';
    });

    // Fonction pour ajouter un plat au panier avec prix
    function ajouterAuPanier(nomPlat, prix) {
        let panier = JSON.parse(localStorage.getItem("panier")) || [];
        panier.push({ nom: nomPlat, prix: prix }); // Stocker le nom et le prix du plat
        localStorage.setItem("panier", JSON.stringify(panier));
        afficherPanier();
    }

    // Fonction pour afficher le contenu du panier et calculer le total
    function afficherPanier() {
        let panier = JSON.parse(localStorage.getItem("panier")) || [];
        let commandeListe = document.getElementById("commande-liste");
        let totalPrix = 0;
        commandeListe.innerHTML = "";

        if (panier.length === 0) {
            commandeListe.innerHTML = "<li>Aucune commande</li>";
        } else {
            panier.forEach((plat, index) => {
                totalPrix += plat.prix; // Additionner les prix des plats

                let li = document.createElement("li");
                li.innerHTML = `
                    <strong>${plat.nom} - ${plat.prix}€</strong> 
                    <button class="supprimer" onclick="supprimerPlat(${index})">Supprimer</button> `;
                commandeListe.appendChild(li);
            });
        }

        // Mettre à jour l'affichage du total
        document.getElementById("total-prix").textContent = `Total : ${totalPrix}€`;
    }

    // Fonction pour supprimer un plat du panier et recalculer le total
    window.supprimerPlat = function(index) {
        let panier = JSON.parse(localStorage.getItem("panier")) || [];
        panier.splice(index, 1);
        localStorage.setItem("panier", JSON.stringify(panier));
        afficherPanier();
    };

    // Fonction pour valider la commande et aller sur une autre page
    window.validerCommande = function() {
        const panier = JSON.parse(localStorage.getItem("panier")) || [];
        const tableChoisie = localStorage.getItem("tableChoisie") || 'N/A';
    
        if (panier.length === 0) {
            alert("Votre commande est vide !");
            return;
        }
    
        // Enregistrement clair de la commande
        let commandes = JSON.parse(localStorage.getItem('commandes')) || [];
    
        commandes.push({
            table: tableChoisie,
            produits: panier,
            paye: false
        });
    
        localStorage.setItem('commandes', JSON.stringify(commandes));
    
        // Vider le panier après validation
        localStorage.removeItem('panier');
    
        // Redirection vers confirmation détaillée
        window.location.href = 'confirmation.html';
    };
    

    // Afficher le panier dès que la page est chargée
    afficherPanier();

    // Fonction pour enregistrer la table choisie
    function choisirTable(table) {
        localStorage.setItem("tableChoisie", table);
        document.getElementById("table-numero").textContent = table;
    }

    // Attache l'événement à chaque bouton "Ajouter au Panier"
    const buttons = document.querySelectorAll('.ajouter-panier');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            let plat = this.previousElementSibling.textContent; // Récupère le nom du plat
            let prix = parseInt(this.getAttribute("data-prix")); // Récupère le prix du bouton
            ajouterAuPanier(plat, prix);
        });
    });

    // Fonction pour afficher/masquer les détails
    window.afficherDetails = function(platId) {
        let overlay = document.getElementById(platId + '-details');

        if (overlay) {
            overlay.style.display = (overlay.style.display === 'flex') ? 'none' : 'flex';
        } else {
            console.log("Élément non trouvé : " + platId + '-details');
        }
    };

    // Fonction pour fermer les détails
    window.fermerDetails = function(platId) {
        let overlay = document.getElementById(platId + '-details');

        if (overlay) {
            overlay.style.display = 'none';
        }
    };
});