const selectStock = document.getElementById("custom_stock");
const stockNameInput = document.getElementById("stock_name");

selectStock.addEventListener("change", function () {
    const option = selectStock.selectedOptions[0]
    const stockNameValue = option.dataset.stockName; 
    stockNameInput.value = stockNameValue;
});