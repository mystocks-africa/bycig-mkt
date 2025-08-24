from classes.RedisClient import RedisClient

class LimitOrder:
    def __init__(self, stock, current_price):
        self.stock = stock
        self.redisClient = RedisClient()
        self.currentPrice = current_price
    
    def findMatchingOrders(self):
        """Find all limit orders that are <= current price (buy orders triggered)"""

        limitOrder = self.redisClient.getLimitOrder(self.stock, self.currentPrice)  

        return limitOrder if limitOrder else "No matching limit orders found."