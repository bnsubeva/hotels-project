from models.reservation import Reservation
from storage.json_storage import JsonStorage

class ReservationService:
    FILE_PATH = "data/reservations.json"

    def __init__(self):
        self.reservations = []

        data = JsonStorage.load(self.FILE_PATH)

        for item in data:
            reservation = Reservation(
                item["id"],
                item["customer_name"],
                item["hotel_id"],
                item["room_id"],
                item["nights"]
            )
            self.reservations.append(reservation)

    def get_reservations(self):
        return self.reservations

    def add_reservation(self, customer_name, hotel_id, room_id, nights):
        reservation = Reservation(
            self.get_next_id(),
            customer_name,
            hotel_id,
            room_id,
            nights
        )
        self.reservations.append(reservation)
        self.save_reservations()

    def delete_reservation(self, reservation_id):
        self.reservations = [
            reservation for reservation in self.reservations
            if reservation.id != reservation_id
        ]
        self.save_reservations()

    def delete_reservations_by_hotel(self, hotel_id):
        self.reservations = [
            reservation for reservation in self.reservations
            if reservation.hotel_id != hotel_id
        ]
        self.save_reservations()

    def delete_reservations_by_room(self, room_id):
        self.reservations = [
            reservation for reservation in self.reservations
            if reservation.room_id != room_id
        ]
        self.save_reservations()

    def get_next_id(self):
        if not self.reservations:
            return 1

        return max(reservation.id for reservation in self.reservations) + 1

    def save_reservations(self):
        data = []

        for reservation in self.reservations:
            data.append(reservation.to_dict())

        JsonStorage.save(self.FILE_PATH, data)
