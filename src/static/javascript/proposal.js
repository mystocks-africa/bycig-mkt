document.addEventListener("DOMContentLoaded", () => {
    fetch(`json-api/proposal/proposal_details.php?proposal_id=${window.serverData.proposal_id}`)
        .then(response => response.json())
        .then(proposal => {
            const container = document.getElementById("proposal-container");

            const card = document.createElement("div");
            card.className = "card";

            card.innerHTML = `
                <h3 class="truncate">${proposal.subject_line}</h3>
                <p><strong>Email:</strong> <span class="truncate">${proposal.email}</span></p>
                <p><strong>Stock:</strong> <span>${proposal.stock_name}</span> (<span>${proposal.stock_ticker}</span>)</p>
                <p><strong>Bid Price:</strong> $${proposal.bid_price}</p>
                <p><strong>Target Price:</strong> $${proposal.target_price}</p>
                <p><strong>Status:</strong> ${proposal.status}</p>
                <p><strong>Thesis:</strong> ${proposal.thesis}</p>
                <p><strong>File:</strong> ${proposal.proposal_file}</p>
            `;

            container.appendChild(card);
        })
        .catch(err => {
            console.error("Failed to load proposal:", err);
            alert("Failed to load proposal. Check console for details.");
        });
});
