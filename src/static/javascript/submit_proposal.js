let currentBatchNumber = 0;

// Runs as soon as the HTML page loads
document.addEventListener("DOMContentLoaded",() => {
    fetchClusterLeaders();
    
});

function appendNewSelectChild(content, value, element) {
    if (!value) {
        value = content; // If no value is provided, use content as value
    }
    
    const option = document.createElement("option");
    option.value = value;
    option.textContent = content;
    element.appendChild(option)
}

function fetchClusterLeaders() {
    fetch("json-api/cluster_leader.php")
    .then(response => response.json())
    .then(clusterLeader => {
        const dropdown = document.getElementById("leaderSelect")

        clusterLeader.forEach(leader => {
            appendNewSelectChild(leader, null, dropdown)
        });
    })
    .catch(error => {
        alert("Error" + error)
    });
}
