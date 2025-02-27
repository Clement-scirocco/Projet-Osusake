document.addEventListener("DOMContentLoaded", function() {
    console.log("Menu chargé !");

    // Récupérer la table choisie depuis localStorage
    let tableChoisie = localStorage.getItem("tableChoisie");

    if (tableChoisie) {
        document.getElementById("table-numero").textContent = tableChoisie;
    } else {
        document.getElementById("table-numero").textContent = "Aucune";
    }

    // Afficher le panier dès le chargement de la page
    afficherPanier();

    // Attacher les événements aux boutons "Ajouter au Panier"
    document.querySelectorAll('.ajouter-panier').forEach(button => {
        button.addEventListener('click', function() {
            let nom = this.getAttribute("data-nom");
            let prix = parseFloat(this.getAttribute("data-prix"));
            ajouterAuPanier(nom, prix);
        });
    });

    // Attacher les événements aux boutons "Information"
    document.querySelectorAll('.info-btn').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.getAttribute("data-id");
            afficherDetails(id);
        });
    });
});

// Tableau contenant la commande
let commande = [];

function ajouterAuPanier(nom, prix) {
    let platExiste = commande.find(plat => plat.nom === nom);
    if (platExiste) {
        platExiste.quantite++;
    } else {
        commande.push({ nom, prix, quantite: 1 });
    }

    afficherPanier();
}

function afficherPanier() {
    let panierListe = document.getElementById("commande-liste");
    let totalPrixElement = document.getElementById("total-prix");
    panierListe.innerHTML = "";

    let total = 0;
    commande.forEach((plat, index) => {
        let li = document.createElement("li");
        li.innerHTML = `${plat.nom} (x${plat.quantite}) - ${plat.prix * plat.quantite}€ 
            <button class="supprimer-btn" onclick="supprimerDuPanier(${index})">❌</button>`;
        panierListe.appendChild(li);
        total += plat.prix * plat.quantite;
    });

    if (commande.length === 0) {
        panierListe.innerHTML = "<li>Aucune commande</li>";
    }

    totalPrixElement.innerText = `Total : ${total}€`;
}

function supprimerDuPanier(index) {
    if (commande[index].quantite > 1) {
        commande[index].quantite--;
    } else {
        commande.splice(index, 1);
    }

    afficherPanier();
}

function afficherDetails(id) {
    let details = document.getElementById(id + "-details");
    details.classList.add("active");
}

function fermerDetails(id) {
    let details = document.getElementById(id + "-details");
    details.classList.remove("active");
}

function validerCommande() {
    if (commande.length === 0) {
        alert("Votre commande est vide !");
        return;
    }

    let tableNumero = document.getElementById("table-numero").innerText;
    let confirmation = `Commande validée pour la table ${tableNumero} !\n\nDétails :\n`;

    commande.forEach(plat => {
        confirmation += `- ${plat.nom} (x${plat.quantite}) - ${plat.prix * plat.quantite}€\n`;
    });

    confirmation += `\nTotal : ${document.getElementById("total-prix").innerText}`;
    alert(confirmation);

    commande = [];
    afficherPanier();
}
