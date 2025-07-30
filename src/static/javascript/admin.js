document.addEventListener("DOMContentLoaded", function () {
    const jwtToken = prompt("Token:")

    if (jwtToken) {
        fetch(`admin.php?jwt=${jwtToken}`)
          .then(response => {
            if (response.redirected) {
            window.location.href = response.url;  // follow the redirect if needbe
            }
        })
        .then(() => fetch("admin.php?get_proposal_info=true"))
        .then(response => response.json())
        .then(proposal => {
            const divElement = document.getElementById("content")
            
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
    } else {
        // Create a full page overlay to block user interaction
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = "100vw";
        overlay.style.height = "100vh";
        overlay.style.backgroundColor = "rgba(0,0,0,0.8)";
        overlay.style.zIndex = 9999;
        overlay.style.color = "white";
        overlay.style.display = "flex";
        overlay.style.justifyContent = "center";
        overlay.style.alignItems = "center";
        overlay.style.fontSize = "2rem";
        overlay.innerText = "Access Denied. Reload the page to try again.";

        document.body.appendChild(overlay);
    }
});

function handleSubmit(acceptOrDecline) {
    if (acceptOrDecline !== "accept" && acceptOrDecline !== "decline") {
        return null;
    }

    fetch('admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `decline_or_accept=${encodeURIComponent(acceptOrDecline)}`
    });
}
