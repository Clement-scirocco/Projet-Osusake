<?php
session_start();

// Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake";

$conn = new mysqli($host, $user, $password, $dbname);

// Vérifier la connexion à la base de données
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si une table et une commande existent dans la session
if (!isset($_SESSION["table"]) || !isset($_SESSION["commande"])) {
    echo "Aucune commande en cours.";
    exit();
}

$table_id = $_SESSION["table"];
$commande_plats = $_SESSION["commande"];

// Étape 1 : Créer une nouvelle commande dans la table `Commandes`
$sql_commande = "INSERT INTO Commandes (table_id, statut) VALUES (?, 'En attente')";
$stmt_commande = $conn->prepare($sql_commande);
$stmt_commande->bind_param("i", $table_id);

if ($stmt_commande->execute()) {
    // Récupérer l'ID de la commande créée
    $commande_id = $stmt_commande->insert_id;

    // Étape 2 : Insérer les plats choisis dans la table `Commandes_Plats`
    $sql_plats = "INSERT INTO Commandes_Plats (commande_id, plat_id, quantite) VALUES (?, ?, ?)";
    $stmt_plats = $conn->prepare($sql_plats);

    foreach ($commande_plats as $plat) {
        // Récupérer l'ID du plat depuis la table `Plats`
        $sql_select_plat = "SELECT plat_id FROM Plats WHERE nom = ?";
        $stmt_select_plat = $conn->prepare($sql_select_plat);
        $stmt_select_plat->bind_param("s", $plat["nom"]);
        $stmt_select_plat->execute();
        $result = $stmt_select_plat->get_result();

        if ($result->num_rows > 0) {
            $plat_data = $result->fetch_assoc();
            $plat_id = $plat_data["plat_id"];

            // Insérer dans Commandes_Plats
            $stmt_plats->bind_param("iii", $commande_id, $plat_id, $plat["quantite"]);
            $stmt_plats->execute();
        }
    }

    // Étape 3 : Vider la commande en session
    unset($_SESSION["commande"]);

    // Rediriger ou afficher un message de succès
    echo "Commande validée avec succès !";
    echo "<a href='menu.php'>Retour au menu</a>";
} else {
    echo "Erreur lors de la création de la commande : " . $conn->error;
}

$conn->close();
?>
