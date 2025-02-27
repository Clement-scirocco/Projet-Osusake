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

// Gestion de la sélection de table
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["table_id"])) {
    $table_id = (int)$_POST["table_id"];

    // Insérer une nouvelle commande associée à cette table
    $sql = "INSERT INTO Commandes (table_id, statut) VALUES (?, 'En attente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $table_id);

    if ($stmt->execute()) {
        // Récupérer l'ID de la commande créée
        $commande_id = $stmt->insert_id;

        // Stocker les informations de table et de commande dans la session
        $_SESSION["table"] = $table_id;
        $_SESSION["commande_id"] = $commande_id;

        // Rediriger vers la page menu
        header("Location: menu.php");
        exit();
    } else {
        echo "Erreur lors de la création de la commande : " . $conn->error;
    }
}

// Récupérer la liste des tables disponibles
$tables = [];
$sql = "SELECT * FROM Tables";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
} else {
    die("Erreur SQL : " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant Asiatique - Choix de Table</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>

    <a href="index.php" class="boutonaccueil">Accueil</a>

<header>
    <h1>Bienvenue chez OSusake</h1>
    <p>Une expérience culinaire authentique au cœur de l'Asie</p>
</header>

<main>
    <section class="titre">
        <h2>Veuillez choisir votre table</h2>
    </section>

    <!-- Affichage des tables disponibles sous forme de boutons dynamiques -->
    <section class="tables-selection">
        <?php if (!empty($tables)): ?>
            <form method="POST" action="tables.php">
                <?php foreach ($tables as $table): ?>
                    <button type="submit" name="table_id" value="<?= htmlspecialchars($table['table_id']) ?>" class="bouton">
                        Table <?= htmlspecialchars($table['numero_table']) ?>
                    </button>
                <?php endforeach; ?>
            </form>
        <?php else: ?>
            <p>Aucune table disponible.</p>
        <?php endif; ?>
    </section>
</main>

<script src="scripttables.js"></script>
</body>
</html>
