document.addEventListener('DOMContentLoaded', () => {
    const commandes = JSON.parse(localStorage.getItem('commandes')) || [];
    const mainContent = document.querySelector('.main-content');

    let selectedCommandeIndex = null; // Stocke l'index de la commande sélectionnée

    commandes.forEach((commande, index) => {
        const totalPrix = commande.produits.reduce((total, plat) => total + plat.prix, 0);
        const reductionAppliquee = commande.reduction ? commande.reduction : 0;
        const totalFinal = totalPrix - reductionAppliquee;

        const commandeHTML = `
            <div class="commande-container" data-index="${index}">
                <div class="table-number">${commande.table}</div>
                <ul class="order-details">
                    ${commande.produits.map(p => `<li>${p.nom} - ${p.prix}€</li>`).join('')}
                </ul>
                <p class="reduction-appliquee" id="reduction-${index}" 
                   style="color: red; font-weight: bold; ${reductionAppliquee > 0 ? '' : 'display: none;'}">
                   Réduction appliquée : -${reductionAppliquee.toFixed(2)}€
                </p>
                <div class="total-prix" id="total-${index}"><strong>Total : ${totalFinal.toFixed(2)}€</strong></div>
                <div class="actions">
                    <button class="ticket">Ticket</button>
                    <button class="reduction" data-index="${index}">Réduction</button>
                    <button class="paiement">Paiement</button>
                    <button class="paye unpaid">Payé</button>
                    <button class="close">❌</button>
                </div>
            </div>`;
        mainContent.insertAdjacentHTML('beforeend', commandeHTML);
    });

    // Gérer les actions des boutons
    mainContent.addEventListener('click', function(e) {
        const commandeContainer = e.target.closest('.commande-container');
        const index = commandeContainer.getAttribute('data-index');

        if (e.target.classList.contains('paiement')) {
            commandes[index].paye = true;
            localStorage.setItem('commandes', JSON.stringify(commandes));
            e.target.nextElementSibling.classList.replace('unpaid', 'paid');
            e.target.nextElementSibling.textContent = 'Payé ✓';
        }

        if (e.target.classList.contains('close')) {
            if (confirm('Supprimer cette commande ?')) {
                commandes.splice(index, 1);
                localStorage.setItem('commandes', JSON.stringify(commandes));
                commandeContainer.remove();
            }
        }

        if (e.target.classList.contains('ticket')) {
            window.location.href = `ticket.html?table=${commandes[index].table}`;
        }

        if (e.target.classList.contains('reduction')) {
            selectedCommandeIndex = index;
            document.getElementById("reduction-modal").style.display = "block";
        }
    });

    // Gérer l'application de la réduction
    document.getElementById("apply-reduction").addEventListener("click", function() {
        if (selectedCommandeIndex !== null) {
            let reductionValue = document.getElementById("reduction-input").value.trim();
            let totalPrixElem = document.getElementById(`total-${selectedCommandeIndex}`);
            let reductionElem = document.getElementById(`reduction-${selectedCommandeIndex}`);
            let totalPrix = commandes[selectedCommandeIndex].produits.reduce((total, plat) => total + plat.prix, 0);
            let reductionAppliquee = 0;

            if (reductionValue.includes("%")) {
                let pourcentage = parseInt(reductionValue);
                reductionAppliquee = (totalPrix * pourcentage / 100);
            } else {
                let montantReduction = parseFloat(reductionValue);
                if (!isNaN(montantReduction)) {
                    reductionAppliquee = montantReduction;
                }
            }

            if (totalPrix - reductionAppliquee < 0) reductionAppliquee = totalPrix;

            let nouveauTotal = totalPrix - reductionAppliquee;

            // Sauvegarde de la réduction
            commandes[selectedCommandeIndex].reduction = reductionAppliquee;
            commandes[selectedCommandeIndex].total = nouveauTotal;
            localStorage.setItem("commandes", JSON.stringify(commandes));

            // Mise à jour de l'affichage
            reductionElem.textContent = `Réduction appliquée : -${reductionAppliquee.toFixed(2)}€`;
            reductionElem.style.display = "block";
            totalPrixElem.innerHTML = `<strong>Total : ${nouveauTotal.toFixed(2)}€</strong>`;

            // Fermer la modale
            document.getElementById("reduction-modal").style.display = "none";
        }
    });

    // Fermer la modale quand on clique sur la croix
    document.querySelector(".close-modal").addEventListener("click", function() {
        document.getElementById("reduction-modal").style.display = "none";
    });
});