<?php
session_start();

// ✅ Connexion à la base de données
$host = "mysql-boulbayem.alwaysdata.net";
$user = "boulbayem";
$password = "steloi123";
$dbname = "boulbayem_susake2";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Erreur de connexion : " . $conn->connect_error);
}

// ✅ Vérifier si la table et la commande existent
if (!isset($_SESSION["table"]) || !isset($_SESSION["commande_id"])) {
    die("⚠️ ERREUR : Table ou commande introuvable !");
}

$table_id = $_SESSION["table"];
$commande_id = $_SESSION["commande_id"];

// ✅ METTRE À JOUR LE STATUT DE LA COMMANDE
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sqlUpdate = "UPDATE Commandes SET statut = 'Payée' WHERE commande_id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("i", $commande_id);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // ✅ Rediriger vers le bar après validation
    header("Location: bar.php");
    exit();
}

// ✅ Récupérer les plats commandés
$sql = "SELECT plat_nom, boisson_nom, dessert_nom, quantite, prix FROM Vue_Commande_Table WHERE commande_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $commande_id);
$stmt->execute();
$result = $stmt->get_result();

$commande = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $nomProduit = $row["plat_nom"] ?: $row["boisson_nom"] ?: $row["dessert_nom"];
    $commande[] = [
        "nom" => $nomProduit,
        "quantite" => $row["quantite"],
        "prix" => $row["prix"]
    ];
    $total += $row["prix"] * $row["quantite"];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>
    <div class="commande-details-container">
        <h2>Merci pour votre commande !</h2>
        <p>Voici les détails de votre commande :</p>
        <ul id="commande-liste"></ul>
        <p id="total-prix">Total : 0€</p>
        <button onclick="retourAccueil()" class="accueil-btn">Retour au menu</button>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log("Chargement des données...");

            // ✅ 2️⃣ Récupération des plats commandés
            let commande = JSON.parse(localStorage.getItem("commande")) || [];
            let commandeListe = document.getElementById("commande-liste");
            commandeListe.innerHTML = "";

            let total = 0;

            // ✅ 3️⃣ Affichage des articles commandés et calcul du total
            commande.forEach(plat => {
                let li = document.createElement("li");
                let prixTotal = plat.prix * plat.quantite;
                li.innerText = `${plat.nom} (x${plat.quantite}) - ${prixTotal}€`;
                commandeListe.appendChild(li);
                total += prixTotal;
            });

            // ✅ 4️⃣ Mise à jour du total
            document.getElementById("total-prix").innerText = `Total : ${total.toFixed(2)}€`;
            localStorage.setItem("totalPrix", total.toFixed(2));
        });

        // ✅ 5️⃣ Nettoyage des données et retour au menu
        function retourAccueil() {
            localStorage.removeItem("commande");
            localStorage.removeItem("totalPrix");
            localStorage.removeItem("tableChoisie");
            window.location.href = "menu.php";
        }
    </script>
</body>
</html>
