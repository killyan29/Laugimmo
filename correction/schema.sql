SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  price_per_night DECIMAL(10,2) NOT NULL,
  rooms INT NOT NULL,
  location VARCHAR(180) NOT NULL,
  category VARCHAR(60) NOT NULL DEFAULT 'maison',
  has_pool TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_listings_user_id (user_id),
  KEY idx_listings_created_at (created_at),
  KEY idx_listings_location (location),
  CONSTRAINT fk_listings_user_id
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_photos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  listing_id INT UNSIGNED NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_listing_photos_listing_id (listing_id),
  CONSTRAINT fk_listing_photos_listing_id
    FOREIGN KEY (listing_id) REFERENCES listings (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reservations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  listing_id INT UNSIGNED NOT NULL,
  renter_id INT UNSIGNED NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reservations_listing_id (listing_id),
  KEY idx_reservations_renter_id (renter_id),
  KEY idx_reservations_dates (start_date, end_date),
  CONSTRAINT fk_reservations_listing_id
    FOREIGN KEY (listing_id) REFERENCES listings (id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reservations_renter_id
    FOREIGN KEY (renter_id) REFERENCES users (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  listing_id INT UNSIGNED NOT NULL,
  sender_id INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_messages_listing_id (listing_id),
  KEY idx_messages_sender_id (sender_id),
  KEY idx_messages_receiver_id (receiver_id),
  KEY idx_messages_created_at (created_at),
  CONSTRAINT fk_messages_listing_id
    FOREIGN KEY (listing_id) REFERENCES listings (id)
    ON DELETE CASCADE,
  CONSTRAINT fk_messages_sender_id
    FOREIGN KEY (sender_id) REFERENCES users (id)
    ON DELETE CASCADE,
  CONSTRAINT fk_messages_receiver_id
    FOREIGN KEY (receiver_id) REFERENCES users (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
