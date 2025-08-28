from redis import Redis
from dotenv import load_dotenv
from os import getenv

load_dotenv()

REDIS_URL = getenv("REDIS_URL")

if not REDIS_URL:
    raise ValueError("No Redis URL found. Please set the REDIS_URL environment variable.")

class RedisClient:
    def __init__(self):
        try:
            self.client = Redis.from_url(REDIS_URL)
        except Exception as e:
            raise ConnectionError(f"Failed to connect to Redis: {e}")

    def getLimitOrder(self, stock, price):
        try:
            return self.client.zrangebyscore(stock, price, '+inf')
        except Exception as e:
            print(f"Error getting limit order: {e}")
            return None

    def deleteLimitOrder(self, stock, order_id):
        try:
            self.client.zrem(stock, order_id)
        except Exception as e:
            print(f"Error deleting limit order: {e}")