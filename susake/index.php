<?php
session_start();

// 🔍 Récupérer l'adresse IP de la tablette
$adresse_ip = $_SERVER['REMOTE_ADDR'];


// 📌 Associer une table en fonction de l'IP de la tablette
$tables_associees = [
    "192.168.12.44" => 8, // ⚡ Ton PC/Tablette associée à Table 1
    "127.0.0.1" => 2,
    "192.168.1.11" => 3,
    "192.168.1.12" => 4,
    "192.168.1.13" => 5,
];

// 🚀 Forcer une IP locale si test en local
if ($adresse_ip == "127.0.0.1" || $adresse_ip == "::1") {
    $adresse_ip = "192.168.12.44"; // ⚠️ Mets l'IP de ton PC ici
}

// 📌 Associer une table en fonction de l'IP
$_SESSION["table"] = $tables_associees[$adresse_ip] ?? null;

// Vérifier si la table a été assignée correctement
if (!isset($_SESSION["table"])) {
    die("❌ Aucun numéro de table trouvé pour cette IP.");
} else {
    
}

// ✅ Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake2";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Erreur de connexion : " . $conn->connect_error);
}

$table_id = $_SESSION["table"];

// 🔍 Vérifier si une commande existe déjà pour cette table
$sqlCheck = "SELECT commande_id FROM Commandes WHERE table_id = ? AND statut NOT IN ('Archivée') LIMIT 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $table_id);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    // ✅ Une commande existe déjà, on la récupère
    $stmtCheck->bind_result($commande_id);
    $stmtCheck->fetch();
    $_SESSION["commande_id"] = $commande_id;
  
} else {
    // 🚀 Sinon, créer une nouvelle commande
    $stmtCheck->close();
    
    $sql = "INSERT INTO Commandes (table_id, statut) VALUES (?, 'Non Payée')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $table_id);

    if ($stmt->execute()) {
        $commande_id = $stmt->insert_id;
        $_SESSION["commande_id"] = $commande_id;
       
    } else {
        die("❌ Erreur lors de la création de la commande : " . $conn->error);
    }
    $stmt->close();
}
$stmtCheck->close();
$conn->close();
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant Asiatique - Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="admin">
        <a href="idbar.php">
            <img src="Images/imageadmin.svg" alt="Icone" class="admin-btn">
        </a>
    </div>


<header>
    <h1>Bienvenue chez OSusake</h1>
    <p>Une expérience culinaire authentique au cœur de l'Asie</p>
</header>

<main>
    <section class="titre">
        <h2>Découvrez nos saveurs</h2>
        <p>Venez goûter à des plats venus de Chine, du Japon, du Vietnam, de Thaïlande et plus encore. Nous vous offrons une expérience gastronomique unique, faite de fraîcheur, de diversité et d’authenticité.</p>
    </section>

    <a href="menu.php" class="bouton">Commencer</a>

</main>

</body>
</html>
