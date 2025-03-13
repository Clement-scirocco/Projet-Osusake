document.addEventListener("DOMContentLoaded", function() {
    let commande = JSON.parse(localStorage.getItem("commande")) || [];
    let commandeListe = document.getElementById("commande-liste");
    let totalPrix = 0;
    let tableSelectionnee = localStorage.getItem("tableChoisie") || "Non définie"; // Correction ici

    if (commande.length === 0) {
        commandeListe.innerHTML = "<li>Aucune commande</li>";
    } else {
        commande.forEach(plat => {
            totalPrix += plat.prix * plat.quantite;
            let li = document.createElement("li");
            li.textContent = `${plat.nom} (x${plat.quantite}) - ${plat.prix * plat.quantite}€`;
            commandeListe.appendChild(li);
        });
    }
    document.getElementById("total-prix").textContent = `Total : ${totalPrix.toFixed(2)}€`;
    document.getElementById("table-selectionnee").textContent = `Table sélectionnée : ${tableSelectionnee}`;
});

function retourAccueil() {
    localStorage.removeItem("commande"); 
    localStorage.removeItem("totalPrix");
    localStorage.removeItem("tableChoisie");
    window.location.href = "menu.php"; // Correction ici pour revenir au menu
}
