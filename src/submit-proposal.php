<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require 'vendor/autoload.php';

use React\EventLoop\Loop;
use React\Http\Browser;

$loop = Loop::get();
$browser = new Browser($loop);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);

    // Update session batch count
    $_SESSION["current_batch_number"] = $current_batch_number;

    $loop->addTimer(1.5, function () {
        echo "Timer done (temp)";
    });

    // Short and sweet message
    $message = "Thank you for contributing to BYCIG!";
    header("Location: redirect.php?message=" . urlencode($message) . "&message_type=success");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>BYCIG Stock Proposal Submission</title>
</head>
<body>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <label>First Name:</label>
        <input type="text" name="first_name" required>
        <br><br>
        
        <label>Last Name:</label>
        <input type="text" name="last_name" required>
        <br><br>
        
        <label>Pick a stock:</label>
        <br>
        
        <!-- Option 1: Select from loaded stocks -->
        <input type="radio" id="useSelect" name="stock_option" value="select" checked onchange="toggleStockInput()">
        <label for="useSelect">Choose from list:</label>
        <select id="stockSelect" name="stock_select">
            <option value="">Loading stocks...</option>
        </select>
        <br><br>
        
        <!-- Option 2: Enter custom stock -->
        <input type="radio" id="useCustom" name="stock_option" value="custom" onchange="toggleStockInput()">
        <label for="useCustom">Can't find your ticker? Enter a custom one:</label>
        <input type="text" id="customStock" name="custom_stock" placeholder="e.g. AAPL, TSLA" disabled>
        <br><br>
        
        <!-- Hidden field to send the final stock value -->
        <input type="hidden" id="finalStockName" name="stock_name">
        
        <button type="submit" onclick="setFinalStockValue()">Submit</button>
    </form>

    <button onclick="fetchNewStockBatch()">Fetch more stocks</button>

    <script>
        let currentBatchNumber = 0;

        document.addEventListener("DOMContentLoaded", function() {
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
                        
                        document.querySelector('button[onclick="fetchNewStockBatch()"]').disabled = true;
                        document.querySelector('button[onclick="fetchNewStockBatch()"]').textContent = 'All stocks loaded';
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
    </script>
</body>
</html>