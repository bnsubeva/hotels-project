from models.room import Room
from storage.json_storage import JsonStorage

class RoomService:
    FILE_PATH = "data/rooms.json"

    def __init__(self):
        self.rooms = []

        data = JsonStorage.load(self.FILE_PATH)

        for item in data:
            room = Room(
                item["id"],
                item["hotel_id"],
                item["number"],
                item["capacity"],
                item["price_per_night"]
            )
            self.rooms.append(room)

    def get_rooms(self):
        return self.rooms

    def get_rooms_by_hotel(self, hotel_id):
        return [room for room in self.rooms if room.hotel_id == hotel_id]

    def add_room(self, hotel_id, number, capacity, price_per_night):
        room = Room(self.get_next_id(), hotel_id, number, capacity, price_per_night)
        self.rooms.append(room)
        self.save_rooms()

    def delete_room(self, room_id):
        self.rooms = [room for room in self.rooms if room.id != room_id]
        self.save_rooms()

    def delete_rooms_by_hotel(self, hotel_id):
        self.rooms = [room for room in self.rooms if room.hotel_id != hotel_id]
        self.save_rooms()

    def get_next_id(self):
        if not self.rooms:
            return 1

        return max(room.id for room in self.rooms) + 1

    def save_rooms(self):
        data = []

        for room in self.rooms:
            data.append(room.to_dict())

        JsonStorage.save(self.FILE_PATH, data)
