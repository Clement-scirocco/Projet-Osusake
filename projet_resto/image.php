<?php
// Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si un plat_id est fourni
if (isset($_GET['id'])) {
    $plat_id = intval($_GET['id']);
    $sql = "SELECT image FROM Plats WHERE plat_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $plat_id);
    $stmt->execute();
    $stmt->bind_result($imageData);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Afficher l'image
    header("Content-Type: image/jpeg"); // Adapter le type d'image si nécessaire
    echo $imageData;
}
?>
