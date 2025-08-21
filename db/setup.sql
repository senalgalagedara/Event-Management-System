-- db/setup.sql - Database schema and default data (includes all core SQL queries)
CREATE DATABASE IF NOT EXISTS college_events CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE college_events;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash CHAR(64) NOT NULL, -- SHA-256 hex
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events
CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  location VARCHAR(120) DEFAULT '',
  event_date DATE NOT NULL,
  description TEXT
);

-- Registrations
CREATE TABLE IF NOT EXISTS registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_event (user_id, event_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Default ordinary user (username=uoc, password=uoc) as required
INSERT IGNORE INTO users (username, password_hash, role)
VALUES ('uoc', SHA2('uoc',256), 'user');

-- (Optional) Default admin: admin/admin
INSERT IGNORE INTO users (username, password_hash, role)
VALUES ('admin', SHA2('admin',256), 'admin');

-- Sample events
INSERT INTO events (title, location, event_date, description) VALUES
('Tech Talk: AI Basics', 'Auditorium', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Introductory session on AI.'),
('Workshop: Web Security', 'Lab 2', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Hands-on OWASP basics.'),
('Sports Meet', 'Main Ground', DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'Annual sports events.');

-- Common Queries Reference
-- Authentication
-- SELECT id, username, role FROM users WHERE username=? AND password_hash=?;

-- Users CRUD
-- INSERT INTO users (username, password_hash, role) VALUES (?,?,?);
-- UPDATE users SET role=?, password_hash=? WHERE id=?;
-- UPDATE users SET role=? WHERE id=?;
-- DELETE FROM users WHERE id=?;
-- SELECT id, username, role, created_at FROM users ORDER BY id DESC;

-- Events CRUD
-- INSERT INTO events (title, location, event_date, description) VALUES (?,?,?,?);
-- UPDATE events SET title=?, location=?, event_date=?, description=? WHERE id=?;
-- DELETE FROM events WHERE id=?;
-- SELECT * FROM events ORDER BY event_date DESC;

-- Registrations
-- SELECT id FROM registrations WHERE user_id=? AND event_id=?;
-- INSERT INTO registrations (user_id, event_id) VALUES (?,?);
-- User's events list:
-- SELECT e.title, e.location, e.event_date, e.description
-- FROM registrations r JOIN events e ON e.id=r.event_id
-- WHERE r.user_id=? ORDER BY e.event_date ASC;

-- Reports
-- SELECT e.id, e.title, COUNT(r.id) as total FROM events e
-- LEFT JOIN registrations r ON r.event_id=e.id GROUP BY e.id, e.title
-- ORDER BY total DESC, e.title ASC;
