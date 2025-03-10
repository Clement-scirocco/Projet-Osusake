<?php
session_start();
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake2";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer toutes les additions non payées
$sql = "SELECT a.addition_id, c.table_id, a.total, a.statut, a.date_addition 
        FROM Addition a 
        JOIN Commandes c ON a.commande_id = c.commande_id 
        WHERE a.statut = 'Non Payée'
        ORDER BY a.date_addition DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Additions</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Liste des Additions</h2>
    <table>
        <tr>
            <th>Table</th>
            <th>Total</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>Table <?= htmlspecialchars($row["table_id"]) ?></td>
                <td><?= number_format($row["total"], 2) ?> €</td>
                <td><?= $row["date_addition"] ?></td>
                <td><?= $row["statut"] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
