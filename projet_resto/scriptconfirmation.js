document.addEventListener("DOMContentLoaded", function() {
    let panier = JSON.parse(localStorage.getItem("panier")) || [];
    let commandeListe = document.getElementById("commande-liste");
    let totalPrix = 0;
    let tableSelectionnee = localStorage.getItem("tableChoisie") || "Non sélectionnée";

    if (panier.length === 0) {
        commandeListe.innerHTML = "<li>Aucune commande</li>";
    } else {
        panier.forEach(plat => {
            totalPrix += plat.prix;
            let li = document.createElement("li");
            li.textContent = `${plat.nom} - ${plat.prix}€`;
            commandeListe.appendChild(li);
        });
    }
    document.getElementById("total-prix").textContent = `Total : ${totalPrix}€`;
    document.getElementById("table-selectionnee").textContent = `Table sélectionnée : ${tableSelectionnee}`;
});

function retourAccueil() {
    localStorage.removeItem("panier"); // Vide le panier après la commande
    window.location.href = "index.html";
}
