from websocket import WebSocketApp
import json
from os import getenv
from dotenv import load_dotenv
from classes.LimitOrder import LimitOrder
from classes.Response import Response

load_dotenv()

FINNHUB_API_KEY = getenv("FINNHUB_API_KEY")

if not FINNHUB_API_KEY:
    raise ValueError("No API key found. Please set the FINNHUB_API_KEY environment variable.")

supported_stocks = [
    "BINANCE:BTCUSDT"
]

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
                    symbol = trade.get("s")
                    price = trade.get("p")
                    limit_order = LimitOrder(symbol, int(price))
                    matches = limit_order.findMatchingOrders()
                        
                    if isinstance(matches, str):
                        print(matches)
                    else:
                        print(json.dumps(Response.decode_bytes(matches), indent=2))
            else:
                print(f"Message without trade data: {data}")
                
        except json.JSONDecodeError as e:
            print(f"Failed to parse JSON: {message}")
            print(f"JSON Error: {e}")
        except Exception as e:
            print(f"Error processing message: {e}")

    def on_error(self, ws, error):
        print(f"WebSocket error: {error}")

    def on_close(self, ws, close_status_code, close_msg):
        print(f"WebSocket closed - Status: {close_status_code}, Message: {close_msg}")

    def on_open(self, ws):
        print("WebSocket connection opened")

        for stock in supported_stocks:
            subscribe_message = {
                "type": "subscribe", 
                "symbol": stock
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
