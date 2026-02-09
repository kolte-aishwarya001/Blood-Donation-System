-- ======================================================
-- Database: donation_blood
-- Full structure, sample data, and triggers
-- ======================================================

CREATE DATABASE IF NOT EXISTS donation_blood;
USE donation_blood;

-- ------------------------------------------------------
-- Table: donors
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    city VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    availability ENUM('Available','Unavailable') DEFAULT 'Available',
    last_donation_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------
-- Table: admin_users
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample admin (username: admin, password: admin123)
INSERT INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$10$wH9LQeKjEHTeE0U3p9ueqOc2Db/XVn4WJ7rT5zRZk1EvGg5sY2Gq6')
ON DUPLICATE KEY UPDATE username = username;

-- ------------------------------------------------------
-- Table: users (optional)
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(120) UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('user','hospital') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------
-- Table: blood_stock
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS blood_stock (
    blood_group VARCHAR(5) PRIMARY KEY,
    available_units INT DEFAULT 0
);

-- Initialize stock with sample values
INSERT INTO blood_stock (blood_group, available_units) VALUES
('A+', 5), ('A-', 2), ('B+', 4), ('B-', 1),
('AB+', 2), ('AB-', 0), ('O+', 7), ('O-', 1)
ON DUPLICATE KEY UPDATE available_units = VALUES(available_units);

-- ------------------------------------------------------
-- Sample donors
-- ------------------------------------------------------
INSERT INTO donors (name, age, blood_group, contact, city, email, availability)
VALUES
('John Doe', 28, 'O+', '9876543210', 'Pune', 'john@example.com', 'Available'),
('Priya Sharma', 32, 'A+', '9876500011', 'Mumbai', 'priya@example.com', 'Available'),
('Ravi Kumar', 45, 'B+', '9876500022', 'Pune', 'ravi@example.com', 'Available'),
('Meera Patel', 30, 'A-', '9876500033', 'Ahmedabad', 'meera@example.com', 'Unavailable');

-- ------------------------------------------------------
-- Trigger: After donor update (update stock automatically)
-- ------------------------------------------------------
DELIMITER //

CREATE TRIGGER trg_donor_after_update
AFTER UPDATE ON donors
FOR EACH ROW
BEGIN
    -- If availability changed, update stock
    IF OLD.availability <> NEW.availability THEN
        IF NEW.availability = 'Available' THEN
            INSERT INTO blood_stock(blood_group, available_units)
            VALUES (NEW.blood_group, 1)
            ON DUPLICATE KEY UPDATE available_units = available_units + 1;
        ELSEIF NEW.availability = 'Unavailable' THEN
            UPDATE blood_stock
            SET available_units = GREATEST(available_units - 1, 0)
            WHERE blood_group = NEW.blood_group;
        END IF;
    END IF;
END //

DELIMITER ;

-- ------------------------------------------------------
-- Trigger: After donor insert (add to stock automatically)
-- ------------------------------------------------------
DELIMITER //

CREATE TRIGGER trg_donor_after_insert
AFTER INSERT ON donors
FOR EACH ROW
BEGIN
    IF NEW.availability = 'Available' THEN
        INSERT INTO blood_stock(blood_group, available_units)
        VALUES (NEW.blood_group, 1)
        ON DUPLICATE KEY UPDATE available_units = available_units + 1;
    END IF;
END //

DELIMITER ;
