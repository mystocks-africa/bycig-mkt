supported_stocks = ["AAPL", "MSFT", "GOOGL", "AMZN", "TSLA"]

def place_limit_order(): 
    for stock in supported_stocks:
        print(f"Placing limit order for {stock}")

place_limit_order()