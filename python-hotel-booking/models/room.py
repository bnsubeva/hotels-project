class Room:
    def __init__(self, room_id, hotel_id, number, capacity, price_per_night):
        self.id = room_id
        self.hotel_id = hotel_id
        self.number = number
        self.capacity = capacity
        self.price_per_night = price_per_night

    def to_dict(self):
        return {
            "id": self.id,
            "hotel_id": self.hotel_id,
            "number": self.number,
            "capacity": self.capacity,
            "price_per_night": self.price_per_night
        }
