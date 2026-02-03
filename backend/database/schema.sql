-- Create database if not exists
CREATE DATABASE IF NOT EXISTS madam_portfolio;
USE madam_portfolio;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Albums table
CREATE TABLE IF NOT EXISTS albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    year VARCHAR(10) NOT NULL,
    category ENUM('album', 'acoustic') DEFAULT 'album',
    cover_image VARCHAR(500),
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tracks table
CREATE TABLE IF NOT EXISTS tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT,
    title VARCHAR(255) NOT NULL,
    duration VARCHAR(10) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    category ENUM('album', 'single', 'acoustic') DEFAULT 'album',
    audio_file VARCHAR(500),
    track_number INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
);

-- Singles table (for standalone singles)
CREATE TABLE IF NOT EXISTS singles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration VARCHAR(10) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    cover_image VARCHAR(500),
    release_date VARCHAR(10),
    audio_file VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500),
    category ENUM('performance', 'studio', 'behind', 'general') DEFAULT 'general',
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Videos table
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    video_url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500),
    category ENUM('music', 'live', 'behind') DEFAULT 'music',
    description TEXT,
    duration VARCHAR(10),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tour dates table
CREATE TABLE IF NOT EXISTS tour_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time TIME,
    ticket_url VARCHAR(500),
    status ENUM('active', 'inactive', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- About page content
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
('admin', 'admin@madam-portfolio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample data
INSERT INTO albums (title, year, category, description) VALUES 
('Echoes of Emotion', '2024', 'album', 'A collection of heartfelt melodies that explore the depths of human emotion'),
('Soulful Journey', '2022', 'album', 'An intimate journey through soul, jazz, and blues'),
('Acoustic Sessions', '2020', 'acoustic', 'Raw, unplugged performances captured in intimate settings')
ON DUPLICATE KEY UPDATE title = title;

-- Insert sample tracks
INSERT INTO tracks (album_id, title, duration, artist, category, track_number) VALUES 
(1, 'Whispers of the Soul', '3:45', 'Artist Name', 'album', 1),
(1, 'Midnight Melodies', '4:12', 'Artist Name', 'album', 2),
(1, 'Dancing in the Rain', '3:28', 'Artist Name', 'album', 3),
(2, 'Journey Begins', '3:15', 'Artist Name', 'album', 1),
(2, 'Soul\'s Awakening', '4:45', 'Artist Name', 'album', 2),
(3, 'Unplugged Dreams', '3:08', 'Artist Name', 'acoustic', 1)
ON DUPLICATE KEY UPDATE title = title;

-- Insert sample singles
INSERT INTO singles (title, duration, artist, release_date) VALUES 
('New Beginning', '3:55', 'Artist Name', '2024'),
('Summer Vibes', '3:22', 'Artist Name', '2024'),
('Winter\'s Tale', '4:08', 'Artist Name', '2023')
ON DUPLICATE KEY UPDATE title = title;
