<?php
session_start();

// Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake2";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les commandes en attente
$sql = "SELECT table_id, commande_id, plat_nom, boisson_nom, dessert_nom, quantite, prix 
        FROM Vue_Commande_Table 
        ORDER BY table_id, commande_id";

$result = $conn->query($sql);

// Organiser les commandes par table
$commandes = [];
$totaux = []; // Tableau pour stocker les totaux par table

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $table_id = $row["table_id"];
        $commandes[$table_id][] = $row;

        // Calcul du total par table
        if (!isset($totaux[$table_id])) {
            $totaux[$table_id] = 0;
        }
        $totaux[$table_id] += $row["prix"] * $row["quantite"];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar - Commandes</title>
    <link rel="stylesheet" href="styles5.css">
</head>
<body>

<div class="sidebar">
    <div class="accueil">
        <a href="index.php">
            <img src="Images/logoaccueil.png" alt="Accueil" class="home-btn">
        </a>
    </div>
    <ul>
        <li><a href="bar.php"><img src="Images/Logocommande.svg" alt="Commande" class="menu-icon"> Commande</a></li>
        <li><a href="dashboard.php"><img src="Images/LogoCarte.svg" class="menu-icon"> Carte</a></li>
    </ul>
</div>

<div class="main-content">
    <?php if (!empty($commandes)) : ?>
        <?php foreach ($commandes as $table_id => $commande) : ?>
            <div class="commande-container">
                <div class="table-number"><?= htmlspecialchars($table_id) ?></div>
                
                <!-- Affichage du total de la table -->


                <ul class="order-details">
                    <?php foreach ($commande as $item) : ?>
                        <li>
                            <?= htmlspecialchars($item["plat_nom"] ?: $item["boisson_nom"] ?: $item["dessert_nom"]) ?> 
                            (x<?= htmlspecialchars($item["quantite"]) ?>) - <?= number_format($item["prix"], 2) ?>€
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="total-commande">
                    <strong>Total : <?= number_format($totaux[$table_id], 2) ?>€</strong>
                </div>
                <div class="actions">
                    <button class="ticket">Ticket</button>
                    <button class="reduction">Réduction</button>
                    <button class="paiement">Paiement</button>
                    <button class="paye unpaid">Payé</button>
                    <button class="close">❌</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Aucune commande en attente.</p>
    <?php endif; ?>
</div>

<script src="idbar.js"></script>

</body>
</html>
