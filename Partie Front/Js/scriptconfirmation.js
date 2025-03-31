document.addEventListener("DOMContentLoaded", function() {
    const commandes = JSON.parse(localStorage.getItem("commandes")) || [];
    const derniereCommande = commandes[commandes.length - 1]; // Récupère la dernière commande
    const commandeListe = document.getElementById("commande-liste");
    const totalPrixElem = document.getElementById("total-prix");
    const tableSelectionneeElem = document.getElementById("table-selectionnee");

    commandeListe.innerHTML = '';
    let totalPrix = 0;

    if (!derniereCommande || derniereCommande.produits.length === 0) {
        commandeListe.innerHTML = "<li>Aucune commande</li>";
        totalPrixElem.textContent = "Total : 0€";
        tableSelectionneeElem.textContent = "Table sélectionnée : Non sélectionnée";
    } else {
        derniereCommande.produits.forEach(plat => {
            totalPrix += plat.prix;
            let li = document.createElement("li");
            li.textContent = `${plat.nom} - ${plat.prix}€`;
            commandeListe.appendChild(li);
        });
        totalPrixElem.textContent = `Total : ${totalPrix}€`;
        document.getElementById("table-selectionnee").textContent = `Table sélectionnée : ${derniereCommande.table}`;
    }
});

function retourAccueil() {
    window.location.href = "Index.html";
}