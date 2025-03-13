document.addEventListener("DOMContentLoaded", function () {
    console.log("Menu chargé !");

    // Récupérer et afficher la table sélectionnée depuis la session ou le localStorage
    afficherTableSelectionnee();
    localStorage.setItem("tableChoisie", document.getElementById("table-numero").textContent);



    // Charger et afficher le panier dès le chargement de la page
    chargerPanier();

    // Attacher les événements aux boutons "Ajouter au Panier"
    document.querySelectorAll('.ajouter-panier').forEach(button => {
        button.addEventListener('click', function () {
            let nom = this.getAttribute("data-nom");
            let prix = parseFloat(this.getAttribute("data-prix"));
            ajouterAuPanier(nom, prix);
        });
    });

    // Attacher les événements aux boutons "Information"
    document.querySelectorAll('.info-btn').forEach(button => {
        button.addEventListener('click', function () {
            let id = this.getAttribute("data-id");
            afficherDetails(id);
        });
    });

    // Attacher les événements aux boutons "Supprimer"
    document.querySelectorAll('.supprimer-btn').forEach(button => {
        button.addEventListener('click', function () {
            let index = this.getAttribute("data-index");
            supprimerDuPanier(index);
        });
    });

});


/** 🛒 GESTION DU PANIER */
let commande = [];

/** Ajouter un plat au panier */
function ajouterAuPanier(nom, prix, type) {
    let itemExiste = commande.find(item => item.nom === nom && item.type === type);
    if (itemExiste) {
        itemExiste.quantite++;
    } else {
        commande.push({ nom, prix, quantite: 1, type: type }); // On ajoute le type
    }
    sauvegarderPanier();
    afficherPanier();
}





/** Afficher le panier */
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

/** Supprimer un plat du panier */
function supprimerDuPanier(index) {
    if (commande[index].quantite > 1) {
        commande[index].quantite--;
    } else {
        commande.splice(index, 1);
    }
    sauvegarderPanier();
    afficherPanier();
}

/** Sauvegarder le panier dans le localStorage */
function sauvegarderPanier() {
    localStorage.setItem("commande", JSON.stringify(commande));
}


/** Charger le panier depuis le localStorage */
document.addEventListener("DOMContentLoaded", function () {
    chargerPanier(); // Charger les éléments du panier dès l'ouverture de la page
});

/** Charger le panier depuis localStorage */
function chargerPanier() {
    let panierStocke = localStorage.getItem("commande");
    if (panierStocke) {
        commande = JSON.parse(panierStocke);
        afficherPanier();
    }
}


/** 🔍 AFFICHER / CACHER LA DESCRIPTION DES PLATS */
function afficherDetails(id) {
    let details = document.getElementById(id + "-details");
    details.classList.add("active");
}

function fermerDetails(id) {
    let details = document.getElementById(id + "-details");
    details.classList.remove("active");
}

/** ✅ VALIDATION DE LA COMMANDE */
function validerCommande() {
    if (commande.length === 0) {
        alert("Votre commande est vide !");
        return;
    }

    // Sauvegarde les informations de la commande pour la page de confirmation
    localStorage.setItem("commande", JSON.stringify(commande));
    localStorage.setItem("totalPrix", document.getElementById("total-prix").innerText);
    localStorage.setItem("tableChoisie", document.getElementById("table-numero").innerText);

    // Redirige vers la page de confirmation
    window.location.href = "confirmation.php";
}
function validerCommande() {
    if (commande.length === 0) {
        alert("Votre commande est vide !");
        return;
    }

    let commandeJSON = JSON.stringify(commande);

    // Envoyer les données en AJAX à `menu.php`
    fetch("menu.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "commande=" + encodeURIComponent(commandeJSON)
    }).then(response => {
        if (response.ok) {
            window.location.href = "confirmation.php";
        } else {
            alert("Erreur lors de la validation de la commande !");
        }
    }).catch(error => console.error("Erreur:", error));
}


