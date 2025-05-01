CREATE DATABASE ticket_booking;
USE ticket_booking;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    available_seats INT NOT NULL
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    booked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Insert predefined events
INSERT INTO events (name, event_date, venue, available_seats) VALUES
('Music Concert', '2025-06-10', 'City Hall', 100),
('Tech Expo', '2025-07-01', 'Convention Center', 150),
('Art Fair', '2025-06-20', 'Art Gallery', 80),
('Comedy Show', '2025-05-25', 'Theatre Arena', 120),
('Startup Pitch Night', '2025-08-15', 'Innovation Hub', 90);
