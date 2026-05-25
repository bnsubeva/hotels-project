from services.hotel_service import HotelService
from services.room_service import RoomService
from services.reservation_service import ReservationService

hotel_service = HotelService()
room_service = RoomService()
reservation_service = ReservationService()

while True:
    print("\n===== Hotel Booking System =====")
    print("1. Add Hotel")
    print("2. Show Hotels")
    print("3. Add Room")
    print("4. Show Rooms")
    print("5. Add Reservation")
    print("6. Show Reservations")
    print("0. Exit")

    choice = input("Choose: ")

    if choice == "1":
        name = input("Hotel name: ")
        city = input("City: ")
        stars = int(input("Stars: "))
        price = float(input("Base price per night: "))
        hotel_service.add_hotel(name, city, stars, price)
        print("Hotel added!")

    elif choice == "2":
        for hotel in hotel_service.get_hotels():
            print(f"{hotel.id}. {hotel.name} | {hotel.city} | {hotel.stars} stars | {hotel.price_per_night} EUR")

    elif choice == "3":
        hotel_id = int(input("Hotel ID: "))
        number = input("Room number: ")
        capacity = int(input("Capacity: "))
        price = float(input("Room price per night: "))
        room_service.add_room(hotel_id, number, capacity, price)
        print("Room added!")

    elif choice == "4":
        for room in room_service.get_rooms():
            print(f"{room.id}. Hotel ID: {room.hotel_id} | Room: {room.number} | Capacity: {room.capacity} | {room.price_per_night} EUR")

    elif choice == "5":
        customer_name = input("Customer name: ")
        hotel_id = int(input("Hotel ID: "))
        room_id = int(input("Room ID: "))
        nights = int(input("Nights: "))
        reservation_service.add_reservation(customer_name, hotel_id, room_id, nights)
        print("Reservation added!")

    elif choice == "6":
        for reservation in reservation_service.get_reservations():
            print(f"{reservation.id}. {reservation.customer_name} | Hotel ID: {reservation.hotel_id} | Room ID: {reservation.room_id} | Nights: {reservation.nights}")

    elif choice == "0":
        break

    else:
        print("Invalid choice.")
