import json
import os

class JsonStorage:
    @staticmethod
    def load(file_path):
        if not os.path.exists(file_path):
            return []

        with open(file_path, "r", encoding="utf-8") as file:
            return json.load(file)

    @staticmethod
    def save(file_path, data):
        with open(file_path, "w", encoding="utf-8") as file:
            json.dump(data, file, indent=4, ensure_ascii=False)
