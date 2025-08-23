from websocket import WebSocketApp, enableTrace
import json
from os import getenv
from dotenv import load_dotenv

load_dotenv()

FINNHUB_API_KEY = getenv("FINNHUB_API_KEY")

if not FINNHUB_API_KEY:
    raise ValueError("No API key found. Please set the FINNHUB_API_KEY environment variable.")

supported_stocks = ["BTCUSDT"]

limit_orders = {
    "1": 150.00,
    "2": 145.00,
    "3": 140.00
}

limit_orders = dict(sorted(limit_orders.items(), key=lambda x: x[1]))

class LimitOrder:
    def __init__(self, orders, current_price):
        self.orders = orders
        self.current_price = current_price
    
    def findMatchingOrders(self):
        """Find limit orders that match the current price"""
        matches = []
        for order_id, limit_price in self.orders.items():
            if self.current_price <= limit_price:
                matches.append({
                    "order_id": order_id,
                    "limit_price": limit_price,
                    "current_price": self.current_price
                })
        
        if not matches:
            return "No matching orders found"
        
        return matches

class FinnHubWebSocket:
    def __init__(self):
        self.ws = None

    def on_message(self, ws, message):
        try:
            if not message or message.strip() == "" or message.strip().isdigit():
                return
            
            data = json.loads(message)
            
            if data.get("type") == "ping":
                return
                
            if "data" in data and data["data"]:
                for trade in data["data"]:
                    price = trade.get("p")
                    symbol = trade.get("s") # use this to get limit orders for specific stock (redis later)
                                        
                    if price:
                        limit_order = LimitOrder(limit_orders, price)
                        matches = limit_order.findMatchingOrders()
                        
                        if isinstance(matches, str):
                            print(matches)
                        else:
                            print(json.dumps(matches, indent=2))
            else:
                print(f"Message without trade data: {data}")
                
        except json.JSONDecodeError as e:
            print(f"Failed to parse JSON: {message}")
            print(f"JSON Error: {e}")
        except Exception as e:
            print(f"Error processing message: {e}")
            print(f"Message content: {message}")

    def on_error(self, ws, error):
        print(f"WebSocket error: {error}")

    def on_close(self, ws, close_status_code, close_msg):
        print(f"WebSocket closed - Status: {close_status_code}, Message: {close_msg}")

    def on_open(self, ws):
        print("WebSocket connection opened")
        subscribe_message = {
            "type": "subscribe", 
            "symbol": 'BINANCE:BTCUSDT'
        }
        ws.send(json.dumps(subscribe_message))

    def start(self):
        self.ws = WebSocketApp(
            f"wss://ws.finnhub.io?token={FINNHUB_API_KEY}",  
            on_message=self.on_message,
            on_error=self.on_error,
            on_close=self.on_close
        )
        self.ws.on_open = self.on_open
        
        try:
            self.ws.run_forever()
        except KeyboardInterrupt:
            print("Shutting down...")
        except Exception as e:
            print(f"Connection error: {e}")
