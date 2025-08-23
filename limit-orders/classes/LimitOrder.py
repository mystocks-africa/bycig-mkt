from websocket import WebSocketApp, enableTrace
import json
from os import getenv
from dotenv import load_dotenv
import bisect

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
        # Create sorted lists for binary search
        self.order_items = list(orders.items())  # [(order_id, price), ...]
        self.prices = [price for _, price in self.order_items]  # [price1, price2, ...]
    
    def findMatchingOrders(self):
        """Find all limit orders that are >= current price using binary search"""
        if not self.prices:
            return "No orders available"
        
        # Find the leftmost position where price >= current_price
        # bisect_left returns the insertion point for current_price
        insert_pos = bisect.bisect_left(self.prices, self.current_price)
        
        # All orders from insert_pos onwards have price >= current_price
        matching_orders = []
        
        for i in range(insert_pos, len(self.order_items)):
            order_id, limit_price = self.order_items[i]
            matching_orders.append({
                "order_id": order_id,
                "limit_price": limit_price,
                "current_price": self.current_price
            })
        
        if not matching_orders:
            return "No matching orders found"
        
        return matching_orders
