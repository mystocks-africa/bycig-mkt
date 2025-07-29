document.addEventListener("DOMContentLoaded", function () {
    const jwtToken = prompt("Token:")

    if (jwtToken) {
        fetch(`admin.php?jwt=${jwtToken}`)
          .then(response => {
            if (response.redirected) {
            window.location.href = response.url;  // follow the redirect if needbe
            }
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

    fetch(`admin.php?decline_or_accept=${acceptOrDecline}`)
}