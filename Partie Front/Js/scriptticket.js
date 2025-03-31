document.addEventListener("DOMContentLoaded", function () {
    const commandes = JSON.parse(localStorage.getItem('commandes')) || [];
    const params = new URLSearchParams(window.location.search);
    const table = params.get('table');
    const emailInput = document.getElementById("email");
    const sendButton = document.getElementById("send-email");
    const ticketDetails = document.getElementById("ticket-details");
    const totalTicket = document.getElementById("total-ticket");
    const reductionInfo = document.getElementById("reduction-info");

    if (table) {
        document.getElementById("table-numero").textContent = `Table : ${table}`;
        const commande = commandes.find(cmd => cmd.table === table);

        if (commande) {
            let total = commande.produits.reduce((sum, plat) => sum + plat.prix, 0);
            let reduction = commande.reduction ? commande.reduction : 0;
            let totalFinal = total - reduction;

            ticketDetails.innerHTML = commande.produits.map(plat => {
                return `<li>${plat.nom} - ${plat.prix.toFixed(2)}€</li>`;
            }).join('');

            if (reduction > 0) {
                reductionInfo.innerHTML = `<strong>Réduction appliquée :</strong> -${reduction.toFixed(2)}€`;
            } else {
                reductionInfo.style.display = "none"; // Masquer si aucune réduction
            }

            totalTicket.textContent = `Total : ${totalFinal.toFixed(2)}€`;
        }
    }

    sendButton.addEventListener("click", function () {
        const email = emailInput.value.trim();
        if (!email) {
            alert("Veuillez entrer une adresse email valide.");
            return;
        }

        // Générer le PDF
        html2canvas(document.getElementById("ticket-content")).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF();
            pdf.addImage(imgData, "PNG", 15, 15, 180, 160);
            const pdfData = pdf.output("datauristring");

            // Envoi de l'email avec le PDF
            Email.send({
                SecureToken: "VOTRE_TOKEN_SMTPJS",
                To: email,
                From: "votre-email@example.com",
                Subject: "Votre Ticket de Commande",
                Body: "Bonjour, voici votre ticket de commande.",
                Attachments: [
                    {
                        name: "ticket.pdf",
                        data: pdfData
                    }
                ]
            }).then(message => alert("Ticket envoyé avec succès !"));
        });
    });
});