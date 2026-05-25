from models.hotel import Hotel
from storage.json_storage import JsonStorage

class HotelService:
    FILE_PATH = "data/hotels.json"

    def __init__(self):
        self.hotels = []

        data = JsonStorage.load(self.FILE_PATH)

        for item in data:
            hotel = Hotel(
                item["id"],
                item["name"],
                item["city"],
                item["stars"],
                item["price_per_night"]
            )
            self.hotels.append(hotel)

    def get_hotels(self):
        return self.hotels

    def add_hotel(self, name, city, stars, price_per_night):
        hotel = Hotel(self.get_next_id(), name, city, stars, price_per_night)
        self.hotels.append(hotel)
        self.save_hotels()

    def delete_hotel(self, hotel_id):
        self.hotels = [hotel for hotel in self.hotels if hotel.id != hotel_id]
        self.save_hotels()

    def get_next_id(self):
        if not self.hotels:
            return 1

        return max(hotel.id for hotel in self.hotels) + 1

    def save_hotels(self):
        data = []

        for hotel in self.hotels:
            data.append(hotel.to_dict())

        JsonStorage.save(self.FILE_PATH, data)
