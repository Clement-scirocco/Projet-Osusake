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

// Récupérer les plats depuis la base de données
$plats = [];
$sql = "SELECT plat_id, nom, prix, image, description FROM Plats";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $plats[] = $row;
    }
} else {
    die("Erreur lors de la récupération des plats : " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Restaurant</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

    <!-- Sidebar (menu latéral) -->
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

    <!-- Affichage des plats -->
    <div class="menu-plats">
        <h2>Nos Plats</h2>
        <div class="plats-list">
            <?php foreach ($plats as $index => $plat): ?>
                <div class="plat-item">
                <img src="image.php?id=<?= $plat['plat_id'] ?>&type=plat" alt="<?= htmlspecialchars($plat['nom']) ?>" class="plat-img">


                    <h4><?= htmlspecialchars($plat['nom']) ?> - <?= number_format($plat['prix'], 2) ?>€</h4>

                    <!-- Boutons -->
                    <button class="ajouter-panier" onclick="ajouterAuPanier('<?= htmlspecialchars($plat['nom']) ?>', <?= $plat['prix'] ?>, 'plat')">
                        Ajouter au Panier
                    </button>
                    
                    <button class="info-btn" onclick="afficherDetails('plat<?= $index ?>')">
                        Information
                    </button>

                    <!-- Description cachée -->
                    <div class="details-overlay hidden" id="plat<?= $index ?>-details">
                        <button class="close-btn" onclick="fermerDetails('plat<?= $index ?>')">×</button>
                        <p><?= htmlspecialchars($plat['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Détail de la commande -->
    <div class="commande-details">
        <h3>Commande</h3>
        <p>Table sélectionnée : <span id="table-numero"><?= $_SESSION["table"] ?></span></p>
        <ul id="commande-liste"></ul>
        <p id="total-prix">Total : 0€</p>
        <button onclick="validerCommande()" class="valider-btn">Valider la commande</button>
    </div>

    <script src="scriptmenu.js"></script>

    <?php
    // ⚡ Traitement de la commande lorsqu'elle est validée
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commande"])) {
        $conn = new mysqli($host, $user, $password, $dbname);
        if ($conn->connect_error) {
            die("Erreur de connexion : " . $conn->connect_error);
        }

        $table_id = $_SESSION["table"];
        $commande = json_decode($_POST["commande"], true);

        if (!empty($commande)) {
            // 1️⃣ Insérer une nouvelle commande dans `Commandes`
            $sqlCommande = "INSERT INTO Commandes (table_id) VALUES (?)";
            $stmt = $conn->prepare($sqlCommande);
            $stmt->bind_param("i", $table_id);
            $stmt->execute();
            $commande_id = $stmt->insert_id;
            $stmt->close();

            // 2️⃣ Insérer chaque article dans `Vue_Commande_Table`
            foreach ($commande as $item) {
                $nom = $conn->real_escape_string($item["nom"]);
                $quantite = intval($item["quantite"]);
                $prix = floatval($item["prix"]);
                $type = $item["type"]; // Récupération du type (plat, boisson, dessert)

                // Initialiser les colonnes avec NULL
                $plat_nom = $boisson_nom = $dessert_nom = null;

                // Vérifier le type et insérer dans la bonne colonne
                if ($type === "plat") {
                    $plat_nom = $nom;
                } elseif ($type === "boisson") {
                    $boisson_nom = $nom;
                } elseif ($type === "dessert") {
                    $dessert_nom = $nom;
                }

                // Insérer dans `Vue_Commande_Table`
                $sqlVue = "INSERT INTO Vue_Commande_Table (commande_id, table_id, plat_nom, boisson_nom, dessert_nom, quantite, prix) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sqlVue);
                $stmt->bind_param("iisssid", $commande_id, $table_id, $plat_nom, $boisson_nom, $dessert_nom, $quantite, $prix);
                $stmt->execute();
                $stmt->close();
            }
            // Calculer le total de la commande
$total = 0;
foreach ($commande as $item) {
    $total += $item["prix"] * $item["quantite"];
}

// Insérer l'addition pour cette commande
$sqlAddition = "INSERT INTO Addition (commande_id, total) VALUES (?, ?)";
$stmt = $conn->prepare($sqlAddition);
$stmt->bind_param("id", $commande_id, $total);
$stmt->execute();
$stmt->close();



            // ✅ Redirection vers la page de confirmation après l'insertion
            header("Location: confirmation.php");
            exit();
        }
    }
    ?>

</body>
</html>
