from classes.RedisClient import RedisClient

class LimitOrder:
    def __init__(self, symbol, currentPrice):
        self.symbol = symbol
        self.redisClient = RedisClient()
        self.currentPrice = currentPrice
    
    def findMatchingOrders(self):
        """Find all limit orders that are <= current price (buy orders triggered)"""

        limitOrder = self.redisClient.getLimitOrder(self.symbol, self.currentPrice)          
        return limitOrder if limitOrder else "No matching limit orders found."