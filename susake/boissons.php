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

// Vérifier si une table est sélectionnée
if (!isset($_SESSION["table"])) {
    header("Location: tables.php"); // Redirige vers la page de sélection de table
    exit();
}

// Récupérer les boissons depuis la base de données
$boissons = [];
$sql = "SELECT boisson_id, nom, prix, image, description FROM Boissons";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $boissons[] = $row;
    }
} else {
    die("Erreur lors de la récupération des boissons : " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Boissons</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="sidebar">
        <ul>
            <div class="accueil">
                <a href="index.php">
                    <img src="Images/logoaccueil.png" alt="Icône" class="home-btn">
                </a>
            </div>
            <li><a href="menu.php"><img src="Images/logonouilles.png" alt="Nouilles" class="menu-icon"> Plats</a></li>
            <li><a href="boissons.php"><img src="Images/logoboisson.png" alt="Boissons" class="menu-icon"> Boissons</a></li>
            <li><a href="desserts.php"><img src="Images/logodessert.png" alt="Desserts" class="menu-icon"> Desserts</a></li>
            
        </ul>
    </div>

    <div class="menu-plats">
        <h2>Boissons</h2>
        <div class="plats-list">
            <?php foreach ($boissons as $index => $boisson): ?>
                <div class="plat-item">
                    <!-- Image de la boisson -->
                    <img src="image.php?id=<?= $boisson['boisson_id'] ?>&type=boisson" alt="<?= htmlspecialchars($boisson['nom']) ?>" class="plat-img">

                    <!-- Nom et prix -->
                    <h4><?= htmlspecialchars($boisson['nom']) ?> - <?= number_format($boisson['prix'], 2) ?>€</h4>

                    <!-- Boutons -->
                    <button class="ajouter-panier" onclick="ajouterAuPanier('<?= htmlspecialchars($boisson['nom']) ?>', <?= $boisson['prix'] ?>, 'boisson')">
    Ajouter au Panier
</button>
                    <button class="info-btn" onclick="afficherDetails('boisson<?= $index ?>')">Information</button>

                    <!-- Description cachée -->
                    <div class="details-overlay hidden" id="boisson<?= $index ?>-details">
                        <button class="close-btn" onclick="fermerDetails('boisson<?= $index ?>')">×</button>
                        <p><?= htmlspecialchars($boisson['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="commande-details">
        <button onclick="validerCommande()" class="valider-btn">Valider la commande</button>
        <h3>Commande</h3>
        <p>Table sélectionnée : <span id="table-numero"><?= $_SESSION["table"] ?></span></p>
        <ul id="commande-liste"></ul>
        <p id="total-prix">Total : 0€</p>
    </div>

    <script src="scriptmenu.js"></script>
        <?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commande"])) {
    // Reconnexion à la base de données
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    $table_id = $_SESSION["table"]; // Récupérer la table sélectionnée
    $commande = json_decode($_POST["commande"], true); // Convertir le JSON en tableau PHP

    if (!empty($commande)) {
        // 1️⃣ Insérer la commande dans `Commandes`
        $sqlCommande = "INSERT INTO Commandes (table_id) VALUES (?)";
        $stmt = $conn->prepare($sqlCommande);
        $stmt->bind_param("i", $table_id);
        $stmt->execute();
        $commande_id = $stmt->insert_id; // Récupérer l'ID de la commande insérée
        $stmt->close();

        // 2️⃣ Insérer chaque article dans `Vue_Commande_Table`
        foreach ($commande as $item) {
            $nom = $item["nom"];
            $quantite = $item["quantite"];
            $prix = $item["prix"];

            // Vérifier si c'est un plat, une boisson ou un dessert
            $sqlCheckPlat = "SELECT plat_id FROM Plats WHERE nom = ?";
            $stmt = $conn->prepare($sqlCheckPlat);
            $stmt->bind_param("s", $nom);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $plat_id = $result->fetch_assoc()["plat_id"] ?? null;
            $boisson_nom = $plat_id ? null : $nom; // Si ce n'est pas un plat, alors c'est une boisson
            $dessert_nom = $plat_id || $boisson_nom ? null : $nom; // Si ce n'est ni un plat ni une boisson, c'est un dessert

            // Insérer dans `Vue_Commande_Table`
            $sqlVue = "INSERT INTO Vue_Commande_Table (commande_id, table_id, plat_nom, boisson_nom, dessert_nom, quantite, prix) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlVue);
            $stmt->bind_param("iisssid", $commande_id, $table_id, $nom, $boisson_nom, $dessert_nom, $quantite, $prix);
            $stmt->execute();
            $stmt->close();
        }

        // ✅ Redirection vers la page de confirmation après l'insertion
        header("Location: confirmation.php");
        exit();
    }
}
?>
</body>
</html>
