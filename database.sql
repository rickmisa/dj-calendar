CREATE DATABASE IF NOT EXISTS event_calendar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_calendar;

CREATE TABLE IF NOT EXISTS events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    event_date DATE NOT NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    location VARCHAR(180) NULL,
    category ENUM('work', 'personal', 'meeting', 'birthday', 'holiday') NOT NULL DEFAULT 'work',
    color VARCHAR(7) NOT NULL DEFAULT '#e85d3f',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_event_date (event_date),
    INDEX idx_category (category)
);

INSERT INTO events (title, description, event_date, start_time, end_time, location, category, color) VALUES
('Product design review', 'Review the new event creation flow with the product team.', CURDATE(), '09:30:00', '10:30:00', 'Studio 3', 'meeting', '#e85d3f'),
('Submit project report', 'Final submission deadline for the calendar management system.', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', NULL, 'Online', 'work', '#2d7f83'),
('Team lunch', 'A relaxed lunch with the core team.', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '12:30:00', '14:00:00', 'The Garden Room', 'personal', '#d79b32');
