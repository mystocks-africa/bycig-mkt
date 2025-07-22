let currentBatchNumber = 0;

document.addEventListener("DOMContentLoaded", function () {
    fetchNewStockBatch();
});

function toggleStockInput() {
    const useSelect = document.getElementById('useSelect').checked;
    const stockSelect = document.getElementById('stockSelect');
    const customStock = document.getElementById('customStock');

    if (useSelect) {
        stockSelect.disabled = false;
        customStock.disabled = true;
        customStock.value = '';
    } else {
        stockSelect.disabled = true;
        stockSelect.selectedIndex = 0;
        customStock.disabled = false;
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
    fetch(`stock_cache.php?current_batch_number=${currentBatchNumber}`)
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
                    const option = document.createElement("option");
                    option.value = symbol;
                    option.textContent = symbol;
                    dropdown.appendChild(option);
                });

                currentBatchNumber++;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
