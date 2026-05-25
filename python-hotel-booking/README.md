# Hotel Booking System

This is a simple project for managing hotels, rooms and reservations.

## Technologies

- Python
- Flask
- HTML
- CSS
- JavaScript
- JSON storage

## Structure

```text
app.py                         - Web application
console_app.py                 - Console application
models/hotel.py                - Hotel class
models/room.py                 - Room class
models/reservation.py          - Reservation class
services/hotel_service.py      - Hotel logic
services/room_service.py       - Room logic
services/reservation_service.py - Reservation logic
storage/json_storage.py        - JSON save/load helper
templates/index.html           - Web page
static/style.css               - Design
static/script.js               - Search functionality
data/hotels.json               - Hotel data
data/rooms.json                - Room data
data/reservations.json         - Reservation data
```

## How to run

Install Flask:

```bash
py -m pip install -r requirements.txt
```

Run web app:

```bash
py app.py
```

Open:

```text
http://127.0.0.1:5000
```

Run console app:

```bash
py console_app.py
```
