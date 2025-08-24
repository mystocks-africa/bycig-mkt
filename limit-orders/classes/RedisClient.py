from redis import Redis
from dotenv import load_dotenv
from os import getenv

load_dotenv()

REDIS_URL = getenv("REDIS_URL")

if not REDIS_URL:
    raise ValueError("No Redis URL found. Please set the REDIS_URL environment variable.")

class RedisClient:
    def __init__(self):
        self.client = Redis.from_url(REDIS_URL)

    def setLimitOrder(self, stock, order_id, price):
        self.client.zadd(stock, order_id, price)

    def getLimitOrder(self, stock, price):
        return self.client.zrangebyscore(stock, price, '+inf')

    def deleteLimitOrder(self, stock, order_id):
        self.client.zrem(stock, order_id)