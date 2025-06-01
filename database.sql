-- Create the database
CREATE DATABASE IF NOT EXISTS blood_donation_db;
USE blood_donation_db;

-- Create blood_donors table
CREATE TABLE IF NOT EXISTS blood_donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    contact_number VARCHAR(15),
    location VARCHAR(100) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    location VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('donor', 'recipient', 'admin') NOT NULL,
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create donation_requests table
CREATE TABLE IF NOT EXISTS donation_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    requester_name VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    location VARCHAR(100) NOT NULL,
    status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
    date_requested TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (name, email, password, blood_group, location, phone, role, verified) 
VALUES ('Admin', 'admin@admin.com', '$2y$10$8K1p/30kGYZ36H.2ECi6g.OV5Pmr8ZRuGR5Or.MqFsX6Bw86PWSXC', 'N/A', 'N/A', 'N/A', 'admin', true);
-- Default admin password is 'admin123' 