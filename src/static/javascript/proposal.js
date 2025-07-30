document.addEventListener("DOMContentLoaded", () => {
    fetch(`proposal.php?proposal_id=${window.serverData.proposal_id}&get_proposal=true`)
        .then(response => response.json())
        .then(proposal => {
            console.log(proposal)
        })
})