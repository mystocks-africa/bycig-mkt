document.addEventListener("DOMContentLoaded", () => {
    fetch("index.php?get_all_proposals=true")
        .then(response => response.json())
        .then(proposals => {
            const loader = document.getElementById("loader");
            loader.style.display = "none";
            
            const containerElement = document.getElementById('grid-container');
            containerElement.style.display = 'grid';
            containerElement.style.gridTemplateColumns = 'repeat(auto-fit, minmax(300px, 1fr))';
            containerElement.style.gap = '1rem';
            containerElement.style.padding = '1rem';

            proposals.forEach(proposal => {
                const card = document.createElement('div');
                card.style.border = '1px solid #ccc';
                card.style.borderRadius = '8px';
                card.style.padding = '1rem';
                card.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                card.style.backgroundColor = '#fff';

                card.innerHTML = `
                    <h3>${proposal.subject_line}</h3>
                    <p><strong>Email:</strong> ${proposal.email}</p>
                    <p><strong>Stock:</strong> ${proposal.stock_ticker} - ${proposal.stock_name}</p>
                    <p><strong>Thesis:</strong> ${proposal.thesis}</p>
                    <p><strong>Bid Price:</strong> $${proposal.bid_price}</p>
                    <p><strong>Target Price:</strong> $${proposal.target_price}</p>
                    <p><strong>File:</strong> ${proposal.proposal_file}</p>
                `;

                containerElement.appendChild(card);
            });
        })
        .catch(err => {
            console.error("Failed to load proposals:", err);
        });
});
