class BisectKey:
    def __init__(self, price):
        self.price = price
    def __lt__(self, other):
        return self.price < other["price"]