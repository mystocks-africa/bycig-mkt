document.addEventListener("DOMContentLoaded", () => {
    fetch("index.php?get_all_proposals=true")
    .then(response => response.json())
    .then(proposals => {

    })

    
})