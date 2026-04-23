-- School Tournament Database Setup
-- Run this SQL script in phpMyAdmin or MySQL command line

-- Create Database
CREATE DATABASE IF NOT EXISTS school_tournament;
USE school_tournament;

-- Create Teams Table
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL UNIQUE,
    points INT DEFAULT 0,
    goals_scored INT DEFAULT 0,
    goals_conceded INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Matches Table
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    team1_goals INT NOT NULL,
    team2_goals INT NOT NULL,
    match_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team1_id) REFERENCES teams(id) ON DELETE RESTRICT,
    FOREIGN KEY (team2_id) REFERENCES teams(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data (Optional - Remove if not needed)
-- Insert sample teams
INSERT INTO teams (class_name, points, goals_scored, goals_conceded) VALUES
('Class A1', 6, 8, 3),
('Class B1', 4, 5, 4),
('Class C1', 3, 4, 5),
('Class D1', 0, 2, 7);

-- Insert sample matches
INSERT INTO matches (team1_id, team2_id, team1_goals, team2_goals, match_date) VALUES
(1, 2, 3, 1, '2025-02-10 10:00:00'),
(3, 4, 2, 1, '2025-02-10 12:00:00'),
(1, 3, 2, 2, '2025-02-11 10:00:00'),
(2, 4, 3, 1, '2025-02-11 12:00:00'),
(1, 4, 3, 0, '2025-02-12 10:00:00'),
(2, 3, 1, 0, '2025-02-12 12:00:00');
