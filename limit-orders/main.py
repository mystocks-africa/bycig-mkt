from classes.FinnhubWebSocket import FinnHubWebSocket

class Main: 
    def __init__(self):
        finnHubWebSocket = FinnHubWebSocket()
        finnHubWebSocket.start()

if __name__ == "__main__":
    Main()