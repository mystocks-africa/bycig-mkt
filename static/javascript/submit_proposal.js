let currentBatchNumber = 0;

// Runs as soon as the HTML page loads
document.addEventListener("DOMContentLoaded",() => {
    fetchNewStockBatch();
    fetchClusterLeaders();
    
});

function appendNewSelectChild(content, value, element) {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = content;
    element.appendChild(option)
}

function toggleStockInput() {
    const useSelect = document.getElementById('useSelect').checked;
    
    const stockSelect = document.getElementById('stockSelect');
    const customStock = document.getElementById('customStock');
    const fetchNewStockBtn = document.getElementById('fetchNewStockBtn');

    if (useSelect) {
        stockSelect.disabled = false;
        fetchNewStockBtn.disabled = false;
        customStock.disabled = true;     

        customStock.value = '';
    } else {
        stockSelect.disabled = true;
        customStock.disabled = false;
        fetchNewStockBtn.disabled = true;
        
        stockSelect.selectedIndex = 0;
    }
}

function fetchNewStockBatch() {
    const params = new URLSearchParams({
        current_batch_number: currentBatchNumber
    });

    fetch(`cache/stock_cache.php?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(stockBatch => {
        const dropdown = document.getElementById("stockSelect");

        if (currentBatchNumber === 0) {
            dropdown.innerHTML = '<option value="">Select a stock...</option>';
        }

        if (stockBatch === "no_more_stocks") {
            const noMoreOption = document.createElement("option");
            noMoreOption.value = "no_more";
            noMoreOption.textContent = "--- No more stocks available ---";
            noMoreOption.disabled = true;
            dropdown.appendChild(noMoreOption);

            const fetchButton = document.querySelector('button[onclick="fetchNewStockBatch()"]');
            fetchButton.disabled = true;
            fetchButton.textContent = 'All stocks loaded';
        } else {
            stockBatch.forEach(symbol => {
                appendNewSelectChild(symbol, symbol, dropdown);
            });

            currentBatchNumber++;
        }
    })
    .catch(error => {
        alert("Error: " + error);
    });
}

function fetchClusterLeaders() {
    fetch("json-api/proposal/cluster_leader.php")
    .then(response => response.json())
    .then(clusterLeader => {
        const dropdown = document.getElementById("leaderSelect")

        clusterLeader.forEach(leader => {
            appendNewSelectChild(leader.user_login, leader.id, dropdown)
        });
    })
    .catch(error => {
        alert("Error" + error)
    });
}
