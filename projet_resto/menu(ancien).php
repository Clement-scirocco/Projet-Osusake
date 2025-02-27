<?php
session_start();

// Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake";

$conn = new mysqli($host, $user, $password, $dbname);

// Vérifier la connexion à la base de données
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si une table est sélectionnée
if (!isset($_SESSION["table"])) {
    header("Location: tables.php");
    exit();
}

// Récupérer les plats depuis la base de données
$plats = [];
$sql = "SELECT * FROM Plats";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $plats[] = $row;
    }
} else {
    die("Erreur lors de la récupération des plats : " . $conn->error);
}

// Gérer l'ajout de plats à la commande
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["plat_nom"], $_POST["quantite"])) {
    $plat_nom = trim($_POST["plat_nom"]);
    $quantite = (int)$_POST["quantite"];

    if (!isset($_SESSION["commande"]) || !is_array($_SESSION["commande"])) {
        $_SESSION["commande"] = [];
    }

    $found = false;
    foreach ($_SESSION["commande"] as &$commande) {
        if ($commande["nom"] === $plat_nom) {
            $commande["quantite"] += $quantite;
            $found = true;
            break;
        }
    }
    unset($commande);

    if (!$found) {
        $_SESSION["commande"][] = [
            "nom" => $plat_nom,
            "quantite" => $quantite
        ];
    }
}

// Gérer la suppression d'un plat
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["supprimer_nom"])) {
    $plat_supprimer = trim($_POST["supprimer_nom"]);

    if (!empty($_SESSION["commande"]) && is_array($_SESSION["commande"])) {
        foreach ($_SESSION["commande"] as $key => $commande) {
            if ($commande["nom"] === $plat_supprimer) {
                unset($_SESSION["commande"][$key]); // Supprimer l'entrée
                break;
            }
        }
        // Réindexer le tableau après suppression
        $_SESSION["commande"] = array_values($_SESSION["commande"]);
    }
}

// Préparer le contenu des plats pour le HTML
$plats_html = "";
foreach ($plats as $plat) {
    $plats_html .= "
        <div class='menu-item'>
            <p>" . htmlspecialchars($plat['nom']) . " - " . number_format($plat['prix'], 2) . "€</p>
            <form method='POST' action='menu.php'>
                <input type='hidden' name='plat_nom' value='" . htmlspecialchars($plat['nom']) . "'>
                <input type='number' name='quantite' value='1' min='1'>
                <button type='submit'>Ajouter</button>
            </form>
        </div>
    ";
}

// Préparer le récapitulatif de la commande pour le HTML
$recapitulatif_commande = "";
if (!empty($_SESSION["commande"])) {
    foreach ($_SESSION["commande"] as $commande) {
        $recapitulatif_commande .= "
            <li>
                " . htmlspecialchars($commande["nom"]) . " (x" . htmlspecialchars($commande["quantite"]) . ")
                <form method='POST' action='menu.php' style='display:inline;'>
                    <input type='hidden' name='supprimer_nom' value='" . htmlspecialchars($commande["nom"]) . "'>
                    <button type='submit'>Supprimer</button>
                </form>
            </li>
        ";
    }
} else {
    $recapitulatif_commande = "<li>Aucune commande</li>";
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
    <!-- Sidebar (menu latéral) -->
    <div class="sidebar">
        <ul>
            <div class="accueil">
                <a href="index.php">
                    <img src="Images/logoaccueil.png" alt="Icône" class="home-btn">
                </a>
            </div>
            <li><a href="#"><img src="Images/logomenu.png" alt="Menu" class="menu-icon"> Menu</a></li>
            <li><a href="#"><img src="Images/logonouilles.png" alt="Nouilles" class="menu-icon"> Nouilles</a></li>
            <li><a href="#"><img src="Images/logomenu.png" alt="Petit Creux" class="menu-icon"> Petit Creux</a></li>
            <li><a href="#"><img src="Images/logodessert.png" alt="Desserts" class="menu-icon"> Desserts</a></li>
            <li><a href="#"><img src="Images/logoboisson.png" alt="Boissons" class="menu-icon"> Boissons</a></li>
            <li><a href="#"><img src="Images/goboisson.png" alt="Divertissement" class="menu-icon"> Divertissement</a></li>
        </ul>
    </div>
    <div class="menu-plats">
        <h2>Nouilles</h2>
        <div class="plats-list">
            <div class="plat-item">
                <img src="Images/nouilleslegumes.webp" alt="Nouilles Légumes" class="plat-img">
                <h4>Nouilles Légumes – 8€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Légumes')">Ajouter au Panier</button>
            </div>
            <div class="plat-item">
                <img src="Images/nouillesboeuf.png" alt="Nouilles Bœuf" class="plat-img">
                <h4>Nouilles Bœuf – 13€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Bœuf')">Ajouter au Panier</button>
            </div>
            <div class="plat-item">
                <img src="Images/nouillescanard.png" alt="Nouilles Canard" class="plat-img">
                <h4>Nouilles Canard – 14€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Canard')">Ajouter au Panier</button>
            </div>
            <div class="plat-item">
                <img src="Images/nouillesporc.webp" alt="Nouilles Porc" class="plat-img">
                <h4>Nouilles Porc – 11€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Porc')">Ajouter au Panier</button>
            </div>
            <div class="plat-item"> 
                <img src="Images/nouillestofu.webp" alt="Nouilles Tofu" class="plat-img">
                <h4>Nouilles Tofu – 9€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Tofu')">Ajouter au Panier</button>
            </div>
            <div class="plat-item">
                <img src="Images/nouillesoeuf.jpg" alt="Nouilles Œufs" class="plat-img">
                <h4>Nouilles Œufs – 9€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Œufs')">Ajouter au Panier</button>
            </div>
            <div class="plat-item">
                <img src="Images/nouillescrevettes.webp" alt="Nouilles Crevettes" class="plat-img">
                <h4>Nouilles Crevettes – 12€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Crevettes')">Ajouter au Panier</button>
            </div>
            <div class="plat-item">
                <img src="Images/nouillespoulet.png" alt="Nouilles Poulet" class="plat-img">
                <h4>Nouilles Poulet – 10€</h4>
                <button class="ajouter-panier" onclick="ajouterAuPanier('Nouilles Poulet')">Ajouter au Panier</button>
            </div>
        </div>
    </div>
    <div class="commande-details">
        <h3>Commande</h3>
        <p>Table sélectionnée : <span id="table-numero">Aucune</span></p>
        <ul id="commande-liste">
            <li>Aucune commande</li>
        </ul>
    </div>

    <script src="scriptmenu.js"></script>
</body>
</html>