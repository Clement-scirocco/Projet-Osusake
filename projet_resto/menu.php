<?php
session_start();

// Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si une table est sélectionnée
if (!isset($_SESSION["table"])) {
    header("Location: tables.php"); // Redirige vers la page de sélection de table
    exit();
}

// Récupérer les plats (entre plat_id 8 et 15)
$plats = [];
$sql = "SELECT plat_id, nom, prix, image, description FROM Plats WHERE plat_id BETWEEN 8 AND 15";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $plats[] = $row;
    }
} else {
    die("Erreur lors de la récupération des plats : " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Restaurant</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

<div class="plats-list">
    <?php foreach ($plats as $index => $plat): ?>
        <div class="plat-item">
            <!-- Image du plat -->
            <img src="image.php?id=<?= $plat['plat_id'] ?>" alt="<?= htmlspecialchars($plat['nom']) ?>" class="plat-img">
            
            <!-- Nom et prix du plat -->
            <h4><?= htmlspecialchars($plat['nom']) ?> - <?= number_format($plat['prix'], 2) ?>€</h4>
            
            <!-- Bouton Ajouter au panier -->
            <button class="ajouter-panier" data-nom="<?= htmlspecialchars($plat['nom']) ?>" data-prix="<?= $plat['prix'] ?>">
                Ajouter au Panier
            </button>

            <!-- Bouton pour afficher la description -->
            <button class="info-btn" data-id="plat<?= $index ?>">Information</button>

            <!-- Overlay contenant la description -->
            <div class="details-overlay" id="plat<?= $index ?>-details">
                <button class="close-btn" onclick="fermerDetails('plat<?= $index ?>')">×</button>
                <p><?= htmlspecialchars($plat['description']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="details-overlay" id="plat<?= $index ?>-details">
    <button class="close-btn" onclick="fermerDetails('plat<?= $index ?>')">×</button>
    <p><?= htmlspecialchars($plat['description']) ?></p>
</div>






    <!-- Détail de la commande -->
    <div class="commande-details">
    <button onclick="validerCommande()" class="valider-btn">Valider la commande</button>
    <h3>Commande</h3>
    <p>Table sélectionnée : <span id="table-numero"><?= $_SESSION["table"] ?></span></p>
    <ul id="commande-liste">
        <li>Aucune commande</li>
    </ul>
    <p id="total-prix">Total : 0€</p>
</div>


    <script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("Menu chargé !");

    let commande = [];

    // Fonction pour ajouter un plat au panier
    function ajouterAuPanier(nom, prix) {
        let platExiste = commande.find(plat => plat.nom === nom);
        if (platExiste) {
            platExiste.quantite++;
        } else {
            commande.push({ nom, prix, quantite: 1 });
        }
        afficherPanier();
    }

    // Fonction pour supprimer un plat du panier
    function supprimerDuPanier(nom) {
        let index = commande.findIndex(plat => plat.nom === nom);
        if (index !== -1) {
            if (commande[index].quantite > 1) {
                commande[index].quantite--;
            } else {
                commande.splice(index, 1);
            }
        }
        afficherPanier();
    }

    // Affichage du panier mis à jour
    function afficherPanier() {
        let panierListe = document.getElementById("commande-liste");
        let totalPrixElement = document.getElementById("total-prix");
        panierListe.innerHTML = "";

        let total = 0;
        commande.forEach(plat => {
            let li = document.createElement("li");
            li.innerHTML = `${plat.nom} (x${plat.quantite}) - ${plat.prix * plat.quantite}€`;
            
            // Création du bouton "Supprimer"
            let btnSupprimer = document.createElement("button");
            btnSupprimer.textContent = "❌";
            btnSupprimer.classList.add("supprimer-btn");
            btnSupprimer.setAttribute("data-nom", plat.nom);
            btnSupprimer.addEventListener("click", function() {
                supprimerDuPanier(plat.nom);
            });

            li.appendChild(btnSupprimer);
            panierListe.appendChild(li);
            total += plat.prix * plat.quantite;
        });

        if (commande.length === 0) {
            panierListe.innerHTML = "<li>Aucune commande</li>";
        }

        totalPrixElement.innerText = `Total : ${total}€`;
    }

// Fonction pour afficher la description
function afficherDetails(id) {
    let details = document.getElementById(id + "-details");
    if (details) {
        details.style.display = "block"; // Afficher l'overlay
    }
}

// Fonction pour fermer la description
function fermerDetails(id) {
    let details = document.getElementById(id + "-details");
    if (details) {
        details.style.display = "none"; // Cacher l'overlay
    }
}

// Attacher les événements aux boutons "Information"
document.querySelectorAll('.info-btn').forEach(button => {
    button.addEventListener('click', function() {
        let id = this.getAttribute("data-id");
        afficherDetails(id);
    });
});

// Attacher les événements aux boutons "Fermer"
document.querySelectorAll('.close-btn').forEach(button => {
    button.addEventListener('click', function() {
        let id = this.getAttribute("data-id");
        fermerDetails(id);
    });
});

    // Fonction pour valider la commande
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

    // Attacher les événements aux boutons "Ajouter au panier"
    document.querySelectorAll('.ajouter-panier').forEach(button => {
        button.addEventListener('click', function() {
            let nom = this.getAttribute("data-nom");
            let prix = parseFloat(this.getAttribute("data-prix"));
            ajouterAuPanier(nom, prix);
        });
    });

    // Attacher l'événement au bouton de validation de commande
    document.querySelector('.valider-btn').addEventListener('click', validerCommande);
});

    </script>

</body>
</html>
