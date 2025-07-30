// Wait for EVERYTHING TO FINISH LOADING

window.onload = () => {
    const jwtToken = prompt("Token:")

    fetch(`json-api/validate_token.php?jwt=${jwtToken}`)
          .then(response => {
            if (response.redirected) {
            window.location.href = response.url;  // follow the redirect if needbe
            }
        })
        .then(() => fetch("json-api/proposal_details.php?admin_purpose=true"))
        .then(response => response.json())
        .then(proposal => {
            const loaderElement = document.getElementById("loader");
            loaderElement.style.display = "none";

            const divElement = document.getElementById("content");
            
            const p1 = document.createElement("p");
            p1.innerHTML = `<strong>Email:</strong> ${proposal.email}`;

            const p2 = document.createElement("p");
            p2.innerHTML = `<strong>Stock:</strong> ${proposal.stock_ticker} - ${proposal.stock_name}`;

            const p3 = document.createElement("p");
            p3.innerHTML = `<strong>Subject:</strong> ${proposal.subject_line}`;

            const p4 = document.createElement("p");
            p4.innerHTML = `<strong>Thesis:</strong> ${proposal.thesis}`;

            const p5 = document.createElement("p");
            p5.innerHTML = `<strong>Bid Price:</strong> ${proposal.bid_price}`;

            const p6 = document.createElement("p");
            p6.innerHTML = `<strong>Target Price:</strong> ${proposal.target_price}`;

            const p7 = document.createElement("p");
            p7.innerHTML = `<strong>Proposal File:</strong> <a href="https://www.bycig.org/${proposal.proposal_file}">View</a>`;

            divElement.append(p1,p2,p3,p4,p5,p6,p7);
    })

};

function handleSubmit(acceptOrDecline) {
    if (acceptOrDecline !== "accept" && acceptOrDecline !== "decline") {
        return null;
    }

    fetch('json-api/update_proposal_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `decline_or_accept=${encodeURIComponent(acceptOrDecline)}`
    });
}
