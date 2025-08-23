from os import getenv
from dotenv import load_dotenv
import bisect
from classes.BisectKey import BisectKey

load_dotenv()

FINNHUB_API_KEY = getenv("FINNHUB_API_KEY")

if not FINNHUB_API_KEY:
    raise ValueError("No API key found. Please set the FINNHUB_API_KEY environment variable.")


class LimitOrder:
    def __init__(self, limitOrders, current_price):
        self.limitOrders = limitOrders
        self.currentPrice = current_price
    
    def findMatchingOrders(self):
        """Find all limit orders that are >= current price using binary search"""
        if not self.currentPrice or not self.limitOrders:
            return "No orders available"
        
        if self.currentPrice < max(self.limitOrders[-1]["price"]):
            return "Highest limit order is less than current price"
        
        bisectKey = BisectKey(self.currentPrice)
        # Find the leftmost position where price >= current_price
        idxRangeStart = bisect.bisect_left(self.limitOrders, bisectKey)
        
        # All orders from idxRangeStart onwards have price >= current_price
        matchingOrders = []
        
        for order in self.order_items[idxRangeStart:]:
            matchingOrders.append({
                "order_id": order[0],
                "limit_price": order[1],
                "current_price": self.current_price
            })
        
        if not matchingOrders:
            return "No matching orders found"
        
        return matchingOrders
