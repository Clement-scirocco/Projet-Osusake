<?php
require 'db.php'; // Connexion à la base de données

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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Gestion de la Carte</h1>
        <button class="btn btn-success mb-3" onclick="document.getElementById('addForm').style.display='block'">Ajouter un élément</button>
        
        <div id="addForm" style="display:none;" class="mb-4">
            <form method="POST" class="d-flex gap-2">
                <select name="add_type" class="form-select" required>
                    <option value="Plats">Plat</option>
                    <option value="Boissons">Boisson</option>
                    <option value="Desserts">Dessert</option>
                </select>
                <input type="text" name="add_nom" placeholder="Nom" class="form-control" required>
                <input type="number" step="0.01" name="add_prix" placeholder="Prix" class="form-control" required>
                <textarea name="add_description" placeholder="Description" class="form-control" required></textarea>
                <button type="submit" class="btn btn-success">Ajouter</button>
            </form>
        </div>
        
        <section class="content">
            <div class="row">
                <?php function displayCards($data, $type, $idField) {
                    if (!empty($data)) {
                        foreach ($data as $row): ?>
                            <div class="col-xxl-3 col-xl-4 col-lg-6 col-12 mb-4">
                                <div class="card text-center">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($row['image'] ?? '') ?>" class="rounded-circle mx-auto" alt="Image">
                                    <h5> <?= htmlspecialchars($row['nom']) ?> </h5>
                                    <p> <?= number_format($row['prix'], 2) ?> € </p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-primary" onclick="alert('<?= htmlspecialchars($row['description'] ?? 'Pas de description') ?>')">Description</button>
                                        <form method="POST">
                                            <input type="hidden" name="delete_type" value="<?= $type ?>">
                                            <input type="hidden" name="delete_id" value="<?= $row[$idField] ?>">
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
