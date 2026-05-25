from flask import Flask, render_template, request, redirect, url_for
from services.hotel_service import HotelService
from services.room_service import RoomService
from services.reservation_service import ReservationService

app = Flask(__name__)

hotel_service = HotelService()
room_service = RoomService()
reservation_service = ReservationService()

@app.route("/")
def home():
    hotels = hotel_service.get_hotels()
    rooms = room_service.get_rooms()
    reservations = reservation_service.get_reservations()

    return render_template(
        "index.html",
        hotels=hotels,
        rooms=rooms,
        reservations=reservations
    )

@app.route("/add-hotel", methods=["POST"])
def add_hotel():
    name = request.form.get("name")
    city = request.form.get("city")
    stars = request.form.get("stars")
    price = request.form.get("price")

    if name and city and stars and price:
        hotel_service.add_hotel(name, city, int(stars), float(price))

    return redirect(url_for("home"))

@app.route("/delete-hotel/<int:hotel_id>", methods=["POST"])
def delete_hotel(hotel_id):
    hotel_service.delete_hotel(hotel_id)
    room_service.delete_rooms_by_hotel(hotel_id)
    reservation_service.delete_reservations_by_hotel(hotel_id)

    return redirect(url_for("home"))

@app.route("/add-room", methods=["POST"])
def add_room():
    hotel_id = request.form.get("hotel_id")
    number = request.form.get("number")
    capacity = request.form.get("capacity")
    price = request.form.get("price")

    if hotel_id and number and capacity and price:
        room_service.add_room(int(hotel_id), number, int(capacity), float(price))

    return redirect(url_for("home"))

@app.route("/delete-room/<int:room_id>", methods=["POST"])
def delete_room(room_id):
    room_service.delete_room(room_id)
    reservation_service.delete_reservations_by_room(room_id)

    return redirect(url_for("home"))

@app.route("/add-reservation", methods=["POST"])
def add_reservation():
    customer_name = request.form.get("customer_name")
    hotel_id = request.form.get("hotel_id")
    room_id = request.form.get("room_id")
    nights = request.form.get("nights")

    if customer_name and hotel_id and room_id and nights:
        reservation_service.add_reservation(
            customer_name,
            int(hotel_id),
            int(room_id),
            int(nights)
        )

    return redirect(url_for("home"))

@app.route("/delete-reservation/<int:reservation_id>", methods=["POST"])
def delete_reservation(reservation_id):
    reservation_service.delete_reservation(reservation_id)

    return redirect(url_for("home"))

if __name__ == "__main__":
    app.run(debug=True)
