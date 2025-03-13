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

// Vérifier si on clique sur la croix pour archiver la commande
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["table_id"])) {
    $table_id = intval($_POST["table_id"]);

    // ✅ Archiver la commande en modifiant uniquement son statut
    $sql = "UPDATE Commandes SET statut = 'Archivée' WHERE table_id = ? AND statut != 'Archivée'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true]);
    exit();
}

// Récupérer les commandes (non archivées)
$sql = "SELECT Vue_Commande_Table.*, Commandes.statut 
        FROM Vue_Commande_Table 
        JOIN Commandes ON Vue_Commande_Table.commande_id = Commandes.commande_id
        WHERE Commandes.statut != 'Archivée'
        ORDER BY table_id, commande_id";

$result = $conn->query($sql);

// Organiser les commandes par table
$commandes = [];
$totaux = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $table_id = $row["table_id"];
        $commandes[$table_id][] = $row;

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
                <ul class="order-details">
                    <?php foreach ($commande as $item) : ?>
                        <li>
                            <?= htmlspecialchars($item["plat_nom"] ?: $item["boisson_nom"] ?: $item["dessert_nom"]) ?> 
                            (x<?= htmlspecialchars($item["quantite"]) ?>) - <?= number_format($item["prix"], 2) ?>€
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    
                <div class="actions">
                <div class="total-commande">
    <strong>Total : <?= number_format($totaux[$table_id], 2) ?>€</strong>
</div>
                    <button class="ticket">Ticket</button>
                    <button class="reduction">Réduction</button>
                    
                    <!-- Bouton "Payé" avec couleur dynamique -->
                    <?php 
                    $statut = $commande[0]["statut"]; // On prend le statut de la première commande de la table
                    $btnClass = ($statut === "Payée") ? "payee" : "unpaid"; 
                    $nouveauStatut = ($statut === "Payée") ? "Non Payée" : "Payée";
                    ?>
                    
                    <form method="POST" action="update_statut.php">
                        <input type="hidden" name="commande_id" value="<?= $commande[0]["commande_id"] ?>">
                        <input type="hidden" name="nouveau_statut" value="<?= $nouveauStatut ?>">
                        <button type="submit" class="paye <?= $btnClass ?>"><?= $statut ?></button>
                    </form>

                    <button class="close" onclick="libererTable(<?= $table_id ?>)">❌</button>

                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Aucune commande en attente.</p>
    <?php endif; ?>
</div>
<script>
function libererTable(tableId) {
    if (confirm("Êtes-vous sûr de vouloir libérer cette table ?")) {
        fetch("bar.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "table_id=" + encodeURIComponent(tableId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recharge la page pour mettre à jour l'affichage
            } else {
                alert("Erreur lors de la libération de la table.");
            }
        })
        .catch(error => console.error("Erreur:", error));
    }
}
</script>
<script src="idbar.js"></script>

</body>
</html>
