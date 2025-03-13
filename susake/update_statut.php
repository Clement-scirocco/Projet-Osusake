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

// Vérifier si on reçoit une requête POST avec une commande_id et un nouveau statut
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commande_id"]) && isset($_POST["nouveau_statut"])) {
    $commande_id = intval($_POST["commande_id"]);
    $nouveau_statut = $_POST["nouveau_statut"];

    // Mise à jour du statut dans la base de données
    $sql = "UPDATE Commandes SET statut = ? WHERE commande_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nouveau_statut, $commande_id);
    $stmt->execute();
    $stmt->close();
}

// Redirection vers la page bar.php après la mise à jour
header("Location: bar.php");
exit();
?>
