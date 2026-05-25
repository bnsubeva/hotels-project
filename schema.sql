CREATE DATABASE IF NOT EXISTS hotels_app
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hotels_app;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS role ENUM('admin', 'user') NOT NULL DEFAULT 'user' AFTER password_hash;

CREATE TABLE IF NOT EXISTS locations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  country VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_location (name, country)
);

CREATE TABLE IF NOT EXISTS hotels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  image_url VARCHAR(500) NULL,
  apartments INT NOT NULL DEFAULT 0,
  studios INT NOT NULL DEFAULT 0,
  offices INT NOT NULL DEFAULT 0,
  has_restaurant TINYINT(1) NOT NULL DEFAULT 0,
  has_spa TINYINT(1) NOT NULL DEFAULT 0,
  has_pool TINYINT(1) NOT NULL DEFAULT 0,
  has_discotheque TINYINT(1) NOT NULL DEFAULT 0,
  location_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_hotel_location (name, location_id),
  CONSTRAINT fk_hotels_locations
    FOREIGN KEY (location_id) REFERENCES locations(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

ALTER TABLE hotels
  ADD COLUMN IF NOT EXISTS image_url VARCHAR(500) NULL AFTER name;

INSERT INTO users (username, password_hash, role)
VALUES ('admin', '$2y$10$k1F7l98IPg7UpOQVyxu1x.3x8.KwzkEmU50x1PO6toOhvWQt8H1oa', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';

INSERT INTO users (username, password_hash, role)
VALUES ('user', '$2y$10$RSAs37WEBWOoLyreO8C2M.aZekkKL/WYpSDVMFU/V8RYs5QR5bfcC', 'user')
ON DUPLICATE KEY UPDATE role = 'user';

INSERT INTO locations (name, country) VALUES
('София', 'България'),
('Пловдив', 'България'),
('Варна', 'България'),
('Бургас', 'България'),
('Банско', 'България'),
('Велинград', 'България'),
('Слънчев бряг', 'България'),
('Солун', 'Гърция'),
('Кавала', 'Гърция'),
('Истанбул', 'Турция')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO hotels (
  name, image_url, apartments, studios, offices,
  has_restaurant, has_spa, has_pool, has_discotheque, location_id
)
VALUES
('Хотел Централ', 'assets/photos/hotels/hotel-exterior.jpg', 12, 8, 2, 1, 1, 0, 0, (SELECT id FROM locations WHERE name = 'София' AND country = 'България')),
('Гранд София Резидънс', 'assets/photos/hotels/hotel-lobby.jpg', 28, 14, 6, 1, 1, 1, 0, (SELECT id FROM locations WHERE name = 'София' AND country = 'България')),
('Пловдив Плаза', 'assets/photos/hotels/hotel-room.jpg', 18, 10, 3, 1, 0, 0, 0, (SELECT id FROM locations WHERE name = 'Пловдив' AND country = 'България')),
('Старият град Бутик', 'assets/photos/hotels/hotel-main-lobby.jpg', 7, 11, 1, 1, 0, 0, 0, (SELECT id FROM locations WHERE name = 'Пловдив' AND country = 'България')),
('Морски Бриз', 'assets/photos/hotels/eden-roc-exterior.jpg', 22, 16, 2, 1, 1, 1, 0, (SELECT id FROM locations WHERE name = 'Варна' AND country = 'България')),
('Варна Бийч Клуб', 'assets/photos/hotels/sagamore-lobby.jpg', 34, 20, 4, 1, 1, 1, 1, (SELECT id FROM locations WHERE name = 'Варна' AND country = 'България')),
('Бургас Марина', 'assets/photos/hotels/southbeach-lobby.jpg', 15, 12, 2, 1, 0, 1, 0, (SELECT id FROM locations WHERE name = 'Бургас' AND country = 'България')),
('Пирин Спа Ризорт', 'assets/photos/hotels/granada-luxury-atrium.jpg', 26, 18, 1, 1, 1, 1, 0, (SELECT id FROM locations WHERE name = 'Банско' AND country = 'България')),
('Ски Лодж Банско', 'assets/photos/hotels/hotel-room-nyc.jpg', 10, 24, 0, 1, 1, 0, 1, (SELECT id FROM locations WHERE name = 'Банско' AND country = 'България')),
('Велинград Термал', 'assets/photos/hotels/royal-hawaiian-lobby.jpg', 30, 22, 2, 1, 1, 1, 0, (SELECT id FROM locations WHERE name = 'Велинград' AND country = 'България')),
('Сън Палас', 'assets/photos/hotels/hotel-suite-bedroom.jpg', 40, 30, 5, 1, 1, 1, 1, (SELECT id FROM locations WHERE name = 'Слънчев бряг' AND country = 'България')),
('Егейски изгрев', 'assets/photos/hotels/hotel-suite-living-room.jpg', 16, 9, 1, 1, 0, 1, 0, (SELECT id FROM locations WHERE name = 'Солун' AND country = 'Гърция')),
('Кавала Сий Вю', 'assets/photos/hotels/hotel-rooftop-pool.jpg', 12, 14, 0, 1, 0, 1, 0, (SELECT id FROM locations WHERE name = 'Кавала' AND country = 'Гърция')),
('Босфор Бизнес Хотел', 'assets/photos/hotels/hotel-room-california.jpg', 8, 18, 12, 1, 1, 0, 0, (SELECT id FROM locations WHERE name = 'Истанбул' AND country = 'Турция'))
ON DUPLICATE KEY UPDATE
  hotels.image_url = VALUES(image_url),
  hotels.apartments = VALUES(apartments),
  hotels.studios = VALUES(studios),
  hotels.offices = VALUES(offices),
  hotels.has_restaurant = VALUES(has_restaurant),
  hotels.has_spa = VALUES(has_spa),
  hotels.has_pool = VALUES(has_pool),
  hotels.has_discotheque = VALUES(has_discotheque);
