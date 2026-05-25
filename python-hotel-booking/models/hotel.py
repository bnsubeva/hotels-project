class Hotel:
    def __init__(self, hotel_id, name, city, stars, price_per_night):
        self.id = hotel_id
        self.name = name
        self.city = city
        self.stars = stars
        self.price_per_night = price_per_night

    def to_dict(self):
        return {
            "id": self.id,
            "name": self.name,
            "city": self.city,
            "stars": self.stars,
            "price_per_night": self.price_per_night
        }
