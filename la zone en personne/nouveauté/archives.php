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

$sql = "SELECT Vue_Commande_Table.*, Commandes.statut, Commandes.table_id, Commandes.commande_id
        FROM Vue_Commande_Table
        JOIN Commandes ON Vue_Commande_Table.commande_id = Commandes.commande_id
        WHERE Commandes.archivee = 1
        ORDER BY Commandes.commande_id DESC";

$result = $conn->query($sql);

$archives = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row["commande_id"];
        $archives[$id]["commandes"][] = $row;
        $archives[$id]["statut"] = $row["statut"];
        $archives[$id]["table_id"] = $row["table_id"];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Commandes</title>
    <link rel="stylesheet" href="styles6.css">
</head>
<body>
    <a href="bar.php" class="btn-retour">← Retour aux commandes</a>
    <h1>📦 Historique des Commandes </h1>
    <?php if (!empty($archives)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Articles</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archives as $commande_id => $data) : 
                    $total = 0;
                    $details = [];
                    foreach ($data["commandes"] as $item) {
                        $nom = $item["plat_nom"] ?: $item["boisson_nom"] ?: $item["dessert_nom"];
                        $qte = $item["quantite"];
                        $pu = $item["prix"];
                        $sous_total = $pu * $qte;
                        $total += $sous_total;
                        $details[] = htmlspecialchars($nom) . " x" . $qte . " – " . number_format($pu, 2) . "€";
                    }
                ?>
                    <tr>
                        <td>#<?= $commande_id ?></td>
                        <td><?= implode("<br>", $details) ?></td>
                        <td><?= number_format($total, 2) ?>€</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucune commande archivée.</p>
    <?php endif; ?>
</body>
</html>