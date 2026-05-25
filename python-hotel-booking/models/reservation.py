class Reservation:
    def __init__(self, reservation_id, customer_name, hotel_id, room_id, nights):
        self.id = reservation_id
        self.customer_name = customer_name
        self.hotel_id = hotel_id
        self.room_id = room_id
        self.nights = nights

    def to_dict(self):
        return {
            "id": self.id,
            "customer_name": self.customer_name,
            "hotel_id": self.hotel_id,
            "room_id": self.room_id,
            "nights": self.nights
        }
