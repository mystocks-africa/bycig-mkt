let currentBatchNumber = 0;

document.addEventListener("DOMContentLoaded", function () {
    fetchNewStockBatch();
    fetchClusterLeaders();
    
});

function appendNewSelectChild(content, element) {
    const option = document.createElement("option");
    option.value = content;
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

function setFinalStockValue() {
    const useSelect = document.getElementById('useSelect').checked;
    const finalStockName = document.getElementById('finalStockName');

    if (useSelect) {
        finalStockName.value = document.getElementById('stockSelect').value;
    } else {
        finalStockName.value = document.getElementById('customStock').value;
    }
}

function fetchNewStockBatch() {
    const params = new URLSearchParams({
        current_batch_number: currentBatchNumber
    });

    fetch(`/src/cache/stock_cache.php?${params.toString()}`, {
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
                appendNewSelectChild(symbol, dropdown);
            });

            currentBatchNumber++;
        }
    })
    .catch(error => {
        alert("Error: " + error);
    });
}

function fetchClusterLeaders() {
    fetch("cluster_leader.php")
    .then(response => response.json())
    .then(clusterLeader => {
        const dropdown = document.getElementById("leaderSelect")

        clusterLeader.forEach(leader => {
            appendNewSelectChild(leader, dropdown)
        });
    })
    .catch(error => {
        alert("Error" + error)
    });
}
