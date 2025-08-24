class Response:
    @staticmethod
    def decode_bytes(obj):
        if isinstance(obj, bytes):
            return obj.decode()
        elif isinstance(obj, dict):
            return {k: Response.decode_bytes(v) for k, v in obj.items()}
        elif isinstance(obj, list):
            return [Response.decode_bytes(x) for x in obj]
        else:
            return obj
