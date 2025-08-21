-- db/queries.sql - Collected SQL statements used throughout the app
-- Authentication
SELECT id, username, role FROM users WHERE username=? AND password_hash=?;

-- Users
INSERT INTO users (username, password_hash, role) VALUES (?,?,?);
UPDATE users SET role=?, password_hash=? WHERE id=?;
UPDATE users SET role=? WHERE id=?;
DELETE FROM users WHERE id=?;
SELECT id, username, role, created_at FROM users ORDER BY id DESC;

-- Events
INSERT INTO events (title, location, event_date, description) VALUES (?,?,?,?);
UPDATE events SET title=?, location=?, event_date=?, description=? WHERE id=?;
DELETE FROM events WHERE id=?;
SELECT * FROM events ORDER BY event_date DESC;

-- Registrations
SELECT id FROM registrations WHERE user_id=? AND event_id=?;
INSERT INTO registrations (user_id, event_id) VALUES (?,?);
SELECT e.title, e.location, e.event_date, e.description
FROM registrations r JOIN events e ON e.id=r.event_id
WHERE r.user_id=? ORDER BY e.event_date ASC;

-- Reports
SELECT e.id, e.title, COUNT(r.id) as total FROM events e
LEFT JOIN registrations r ON r.event_id=e.id
GROUP BY e.id, e.title
ORDER BY total DESC, e.title ASC;
