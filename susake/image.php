<?php
// Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake2";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données.");
}

// Vérifier si l'ID et le type sont fournis et valides
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['type'])) {
    die("ID ou type d'image invalide.");
}

$id = intval($_GET['id']);
$type = $_GET['type']; // Type attendu : plat, boisson, dessert

// Sélectionner la bonne table en fonction du type
switch ($type) {
    case 'boisson':
        $sql = "SELECT image FROM Boissons WHERE boisson_id = ?";
        break;
    case 'dessert':
        $sql = "SELECT image FROM Desserts WHERE dessert_id = ?";
        break;
    default: // Plat par défaut
        $sql = "SELECT image FROM Plats WHERE plat_id = ?";
        break;
}

// Exécuter la requête
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($image);
$stmt->fetch();

if ($stmt->num_rows > 0 && !empty($image)) {
    // Détection du type MIME de l'image
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($image) ?: "image/png"; // Si indéterminé, mettre PNG par défaut

    header("Content-Type: " . $mimeType);
    echo $image;
} else {
    // Si l'image est introuvable, afficher une image par défaut
    header("Content-Type: image/png");
    readfile("Images/default.png"); // Remplace par une image par défaut
}

$stmt->close();
$conn->close();
?>
