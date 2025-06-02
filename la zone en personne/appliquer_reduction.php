<?php
session_start();

header('Content-Type: application/json');

$conn = new mysqli("mysql-boulbayem.alwaysdata.net", "boulbayem", "steloi123", "boulbayem_susake2");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion : " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commande_id"], $_POST["reduction"])) {
    $commande_id = filter_var($_POST["commande_id"], FILTER_VALIDATE_INT);
    $reduction = filter_var($_POST["reduction"], FILTER_VALIDATE_FLOAT);

    if ($commande_id === false || $reduction === false || $reduction < 0 || $reduction > 100) {
        echo json_encode(["success" => false, "message" => "Paramètres invalides ou réduction hors limites."]);
        exit;
    }

    // Calcul du total brut à partir de Vue_Commande_Table
    $sqlGetOriginal = "SELECT SUM(prix * quantite) AS total_brut FROM Vue_Commande_Table WHERE commande_id = ?";
    $stmtOrig = $conn->prepare($sqlGetOriginal);
    $stmtOrig->bind_param("i", $commande_id);
    $stmtOrig->execute();
    $stmtOrig->bind_result($total_brut);
    $stmtOrig->fetch();
    $stmtOrig->close();

    if ($total_brut === null) {
        echo json_encode(["success" => false, "message" => "Commande introuvable (aucun total brut)."]);
        $conn->close();
        exit;
    }

    $nouveau_total = round($total_brut * (1 - $reduction / 100), 2);

    // Vérifie si une entrée existe déjà
    $sqlCheck = "SELECT COUNT(*) FROM Addition WHERE commande_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $commande_id);
    $stmtCheck->execute();
    $stmtCheck->bind_result($exists);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($exists > 0) {
        // Mise à jour avec reduction_p
        $sqlUpdate = "UPDATE Addition SET total = ?, reduction_p = ? WHERE commande_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("dii", $nouveau_total, $reduction, $commande_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } else {
        // Insertion d'une nouvelle ligne avec reduction_p
        $sqlInsert = "INSERT INTO Addition (commande_id, total, reduction_p) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("idi", $commande_id, $nouveau_total, $reduction);
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    echo json_encode([
        "success" => true,
        "nouveau_total" => $nouveau_total
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Paramètres manquants."]);
}

$conn->close();
?>