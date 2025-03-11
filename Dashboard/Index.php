<?php
require 'db.php'; // Connexion à la base de données

// Gestion de l'ajout d'un élément
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_type"])) {
    $table = $_POST["add_type"];
    $nom = $_POST["add_nom"];
    $prix = $_POST["add_prix"];
    $description = $_POST["add_description"];

    if (isset($_FILES["add_image"]) && $_FILES["add_image"]["error"] === 0) {
        $imageType = mime_content_type($_FILES["add_image"]["tmp_name"]); // Vérifier le type MIME
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($imageType, $allowedTypes)) {
            $imageData = file_get_contents($_FILES["add_image"]["tmp_name"]); // Récupération des données binaires
        } else {
            $imageData = null; // Type d'image non autorisé
        }
    } else {
        $imageData = null; // Pas d'image envoyée
    }

    // Insertion dans la BDD avec l'image
    $stmt = $pdo->prepare("INSERT INTO $table (nom, prix, description, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $prix, $description, $imageData]);

    // Rafraîchir la page après ajout
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Gestion de la suppression d'un élément
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm_delete_type"]) && isset($_POST["confirm_delete_id"])) {
    $table = $_POST["confirm_delete_type"];
    $id = $_POST["confirm_delete_id"];
    $column_id = ($table == "Plats") ? "plat_id" : (($table == "Boissons") ? "boisson_id" : "dessert_id");

    $stmt = $pdo->prepare("DELETE FROM $table WHERE $column_id = ?");
    $stmt->execute([$id]);

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Gestion de la modification d'un élément
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_type"]) && isset($_POST["edit_id"])) {
    $table = $_POST["edit_type"];
    $id = $_POST["edit_id"];
    $nom = $_POST["edit_nom"];
    $prix = $_POST["edit_prix"];
    $description = $_POST["edit_description"];
    $column_id = ($table == "Plats") ? "plat_id" : (($table == "Boissons") ? "boisson_id" : "dessert_id");

    // Vérification si une nouvelle image a été envoyée
    if (isset($_FILES["edit_image"]) && $_FILES["edit_image"]["error"] === 0) {
        $imageType = mime_content_type($_FILES["edit_image"]["tmp_name"]);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($imageType, $allowedTypes)) {
            $imageData = file_get_contents($_FILES["edit_image"]["tmp_name"]);
            // Mise à jour avec une nouvelle image
            $stmt = $pdo->prepare("UPDATE $table SET nom = ?, prix = ?, description = ?, image = ? WHERE $column_id = ?");
            $stmt->execute([$nom, $prix, $description, $imageData, $id]);
        }
    } else {
        // Mise à jour sans changer l'image
        $stmt = $pdo->prepare("UPDATE $table SET nom = ?, prix = ?, description = ? WHERE $column_id = ?");
        $stmt->execute([$nom, $prix, $description, $id]);
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}



// Récupération des données
$plats = $pdo->query("SELECT * FROM Plats")->fetchAll();
$boissons = $pdo->query("SELECT * FROM Boissons")->fetchAll();
$desserts = $pdo->query("SELECT * FROM Desserts")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Gestion de la Carte</h1>
        <button class="btn btn-success mb-3" onclick="document.getElementById('addForm').style.display='block'">Ajouter un élément (plat, boisson, dessert) </button>
        
        <div id="addForm" style="display:none;" class="mb-4">
        <form method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-2">
        <select name="add_type" class="form-select" required>
        <option value="Plats">Plat</option>
        <option value="Boissons">Boisson</option>
        <option value="Desserts">Dessert</option>
        </select>
        <input type="text" name="add_nom" placeholder="Nom" class="form-control" required>
        <input type="number" step="0.01" name="add_prix" placeholder="Prix" class="form-control" required>
        <textarea name="add_description" placeholder="Description" class="form-control" required></textarea>
    
    <!-- Champ d'image (ajouté) -->
    <input type="file" name="add_image" class="form-control" accept="image/*" required>

    <button type="submit" class="btn btn-success">Ajouter</button>
</form>
</div>

<input type="text" id="searchBar" class="form-control mb-4" placeholder="Rechercher un plat, une boisson ou un dessert..." onkeyup="filterItems()">

        <section class="content">
            <div class="row">
            <?php function displayCards($data, $type, $idField) {
    if (!empty($data)) {
        foreach ($data as $row): ?>
            <div class="col-xxl-3 col-xl-4 col-lg-6 col-12 mb-4">
                <div class="card text-center">
                    
                    <img src="data:image/jpeg;base64,<?= base64_encode($row['image'] ?? '') ?>" 
                         class="rounded-circle mx-auto" 
                         alt="Image"
                         style="width: 100px; height: 100px; object-fit: cover;">

                    <h5> <?= htmlspecialchars($row['nom']) ?> </h5>
                    <p> <?= number_format($row['prix'], 2) ?> € </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-primary" onclick="openDescriptionModal('<?= htmlspecialchars($row['description'] ?? 'Pas de description') ?>')">Description</button>
                        <button class="btn btn-warning" onclick="openEditModal('<?= $type ?>', '<?= $row[$idField] ?>', '<?= htmlspecialchars($row['nom']) ?>', '<?= $row['prix'] ?>', '<?= htmlspecialchars($row['description']) ?>')">Modifier</button>
                        <button class="btn btn-danger" onclick="openDeleteModal('<?= $type ?>', '<?= $row[$idField] ?>')">Supprimer</button>
                    </div>
                </div>
            </div>
        <?php endforeach;
    } else {
        echo "<p class='text-center'>Aucun élément trouvé pour $type.</p>";
    }
} ?>
            </div>
        </section>

        <h2 class="mt-4">Plats</h2>
        <div class="row">
            <?php displayCards($plats, "Plats", "plat_id"); ?>
        </div>
        
        <h2 class="mt-4">Boissons</h2>
        <div class="row">
            <?php displayCards($boissons, "Boissons", "boisson_id"); ?>
        </div>
        
        <h2 class="mt-4">Desserts</h2>
        <div class="row">
            <?php displayCards($desserts, "Desserts", "dessert_id"); ?>
        </div>
    </div>

    <div id="modal-container"></div>

    <script>
        function openDeleteModal(type, id) {
            document.getElementById("modal-container").innerHTML = `
                <div class="modal fade show" style="display:block;">
                    <div class="modal-dialog">
                        <div class="modal-content p-3">
                            <h4 class="text-danger">Confirmation de Suppression</h4>
                            <p>Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
                            <form method="POST">
                                <input type="hidden" name="confirm_delete_type" value="${type}">
                                <input type="hidden" name="confirm_delete_id" value="${id}">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        }

        function openDescriptionModal(description) {
            document.getElementById("modal-container").innerHTML = `
                <div class="modal fade show" style="display:block;">
                    <div class="modal-dialog">
                        <div class="modal-content p-3">
                            <h4>Description</h4>
                            <p>${description}</p>
                            <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
                        </div>
                    </div>
                </div>
            `;
        }

        function closeModal() { document.getElementById("modal-container").innerHTML = ""; }

        function openEditModal(type, id, nom, prix, description) {
        document.getElementById("modal-container").innerHTML = `
        <div class="modal fade show" style="display:block;">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <h4>Modifier</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="edit_type" value="${type}">
                        <input type="hidden" name="edit_id" value="${id}">
                        <div class="mb-2">
                            <label class="form-label">Nom</label>
                            <input type="text" name="edit_nom" value="${nom}" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Prix (€)</label>
                            <input type="number" step="0.01" name="edit_prix" value="${prix}" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Description</label>
                            <textarea name="edit_description" class="form-control">${description}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Changer l'image (facultatif)</label>
                            <input type="file" name="edit_image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-warning">Sauvegarder</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    `;
}


    function closeModal() { 
        document.getElementById("modal-container").innerHTML = ""; 
    }

    function filterItems() {
        let input = document.getElementById("searchBar").value.toLowerCase();
        let cards = document.querySelectorAll(".card");

        cards.forEach(card => {
            let itemName = card.querySelector("h5").innerText.toLowerCase();
            if (itemName.includes(input)) {
                card.parentElement.style.display = "block"; // Afficher la carte
            } else {
                card.parentElement.style.display = "none"; // Cacher la carte
            }
        });
    }
    </script>
</body>
</html>
