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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["table_id"])) {
    $table_id = intval($_POST["table_id"]);

    $sql = "UPDATE Commandes SET table_id = NULL WHERE table_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $table_id);
    $stmt->execute();

    echo json_encode(["success" => true]);
    exit();
}

$sql = "SELECT Vue_Commande_Table.*, Commandes.statut 
        FROM Vue_Commande_Table 
        JOIN Commandes ON Vue_Commande_Table.commande_id = Commandes.commande_id
        WHERE Commandes.statut != 'Archivée' AND Commandes.table_id IS NOT NULL
        ORDER BY table_id, commande_id";

$result = $conn->query($sql);

$commandes = [];
$totaux = [];
$commande_ids = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $table_id = $row["table_id"];
        $commandes[$table_id][] = $row;
        $commande_ids[$row["commande_id"]] = true;

        if (!isset($totaux[$table_id])) {
            $totaux[$table_id] = 0;
        }
        $totaux[$table_id] += $row["prix"] * $row["quantite"];
    }
}

$reductions = [];
if (!empty($commande_ids)) {
    $ids = implode(',', array_keys($commande_ids));
    $sql_reduc = "SELECT commande_id, total, reduction_p FROM Addition WHERE commande_id IN ($ids)";
    $result_reduc = $conn->query($sql_reduc);

    if ($result_reduc) {
        while ($row = $result_reduc->fetch_assoc()) {
            $reductions[$row["commande_id"]] = [
                "total" => floatval($row["total"]),
                "percent" => floatval($row["reduction_p"])
            ];
        }
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
    </ul>
</div>

<div class="main-content">
<?php if (!empty($commandes)) : ?>
    <?php foreach ($commandes as $table_id => $commande) : ?>
        <?php 
        $commande_id = $commande[0]['commande_id'];
        $total_commande = $totaux[$table_id];
        $reduction_p = isset($reductions[$commande_id]) ? $reductions[$commande_id]["percent"] : 0;
        $total_reduit = round($total_commande * (1 - $reduction_p / 100), 2);
        ?>
        <div class="commande-container">
            <div class="table-number"><?= htmlspecialchars($table_id) ?></div>
            <ul class="order-details">
                <?php foreach ($commande as $item) : ?>
                    <?php
                        $nom = $item["plat_nom"] ?: $item["boisson_nom"] ?: $item["dessert_nom"];
                        $qte = $item["quantite"];
                        $pu = $item["prix"];
                        $total_ligne = $qte * $pu;
                        $total_ligne_reduit = round($total_ligne * (1 - $reduction_p / 100), 2);
                    ?>
                    <li>
                        <?= htmlspecialchars($nom) ?> (x<?= $qte ?>) – <?= number_format($pu, 2) ?>€
                        <br>
                        <strong><?= number_format($total_ligne, 2) ?>€</strong>
                        <?php if ($reduction_p > 0): ?>
                            <span style="color:red;">→ <?= number_format($total_ligne_reduit, 2) ?>€ (-<?= $reduction_p ?>%) 🔻</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="actions">
                <div class="total-commande">
                    <strong>Total : <?= number_format($total_reduit, 2) ?>€</strong>
                </div>

                <button class="ticket" data-commande-id="<?= $commande_id ?>">Ticket</button>
                <button class="reduction" data-commande-id="<?= $commande_id ?>">Réduction</button>

                <?php 
                $statut = $commande[0]["statut"];
                $btnClass = ($statut === "Payée") ? "payee" : "unpaid"; 
                $nouveauStatut = ($statut === "Payée") ? "Non Payée" : "Payée";
                ?>
                <form class="update-statut-form">
                    <input type="hidden" name="commande_id" value="<?= $commande_id ?>">
                    <input type="hidden" name="nouveau_statut" value="<?= $nouveauStatut ?>">
                    <button type="button" class="paye <?= $btnClass ?>"><?= $statut ?></button>
                </form>

                <button class="close" onclick="libererTable(<?= $table_id ?>)">❌</button>
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