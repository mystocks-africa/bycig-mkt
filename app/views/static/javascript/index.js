document.addEventListener("DOMContentLoaded", () => {
    fetch("json-api/all_proposals.php?get_all_proposals=true")
        .then(response => response.json())
        .then(proposals => {
            const loader = document.getElementById("loader");
            loader.style.display = "none";

            const containerElement = document.getElementById('grid-container');

            proposals.forEach(proposal => {
                const card = document.createElement('a');
                card.href = `/src/proposal.php?proposal_id=${proposal.post_id}`
                card.className = "card";
                card.style.textDecoration = "none";
                card.style.color = "black";

                card.innerHTML = `
                    <h3 class="truncate">${proposal.subject_line}</h3>
                    <p><strong>Email:</strong> <span class="truncate">${proposal.email}</span></p>
                `;
                containerElement.appendChild(card);
            });
        })
        .catch(err => {
            alert("Failed to load proposals:", err);
        });
});

function goToSubmitProposal() {
    window.location.href = 'submit_proposal.php'
}