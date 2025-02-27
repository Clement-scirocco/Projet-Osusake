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

<header>
    <a href="Accueil.html" class="home-btn">Accueil</a>
    <h1>Menu</h1>
</header>

<main>
    <!-- Section pour afficher les plats -->
    <section class="menu-grid">
        <?= $plats_html ?>
    </section>

    <!-- Section pour le récapitulatif de la commande -->
    <section class="order-summary">
        <div class="ticket-commande">
            <h3>🧾 Détail de la commande</h3>
            <p>Table : <?= htmlspecialchars($_SESSION["table"] ?? "Non spécifiée") ?></p>
            <ul>
                <?= $recapitulatif_commande ?>
            </ul>
        </div>

        <!-- Formulaire pour valider la commande -->
        <form action="commande.php" method="POST">
            <button type="submit" class="validate-btn">Valider mon menu</button>
        </form>
    </section>
</main>

</body>
</html>
