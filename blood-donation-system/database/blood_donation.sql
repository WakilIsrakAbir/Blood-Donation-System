-- ============================================
-- Blood Donation System - Database Schema
-- Stack: MySQL 5.7+
-- ============================================

CREATE DATABASE IF NOT EXISTS blood_donation_db;
USE blood_donation_db;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    district VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    last_donation_date DATE DEFAULT NULL,
    status ENUM('active', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_blood_group (blood_group),
    INDEX idx_district (district),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. BLOOD REQUESTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    hospital_address VARCHAR(255) NOT NULL,
    required_blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units_needed INT NOT NULL DEFAULT 1,
    date_needed DATE NOT NULL,
    urgency ENUM('Normal', 'Urgent', 'Critical') DEFAULT 'Normal',
    status ENUM('Pending', 'Approved', 'Rejected', 'Fulfilled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_urgency (urgency),
    INDEX idx_blood_group (required_blood_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. DONATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    request_id INT NOT NULL,
    status ENUM('Pledged', 'Completed') DEFAULT 'Pledged',
    donated_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
    INDEX idx_donor (donor_id),
    INDEX idx_request (request_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. CONTACT MESSAGES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SEED DATA: Default Admin Account
-- Password: admin123 (hashed with bcrypt)
-- ============================================
INSERT INTO users (name, email, password, role, blood_group, phone, district, age, status)
VALUES (
    'System Admin',
    'admin@blooddonation.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'O+',
    '01700000000',
    'Dhaka',
    30,
    'active'
);

-- ============================================
-- SEED DATA: Sample Users for Testing
-- Password for all: password123
-- ============================================
INSERT INTO users (name, email, password, role, blood_group, phone, district, age, last_donation_date, status) VALUES
('Rahim Uddin', 'rahim@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'A+', '01712345678', 'Dhaka', 25, '2026-01-15', 'active'),
('Karim Hossain', 'karim@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'B+', '01812345678', 'Chittagong', 28, '2026-02-20', 'active'),
('Fatema Begum', 'fatema@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'O-', '01912345678', 'Sylhet', 22, NULL, 'active'),
('Arif Ahmed', 'arif@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'AB+', '01612345678', 'Rajshahi', 30, '2026-03-10', 'active'),
('Nusrat Jahan', 'nusrat@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'A-', '01512345678', 'Khulna', 27, '2025-11-05', 'active');

-- Sample Blood Requests
INSERT INTO blood_requests (user_id, patient_name, hospital_address, required_blood_group, units_needed, date_needed, urgency, status) VALUES
(2, 'Abdul Karim', 'Dhaka Medical College Hospital, Dhaka', 'A+', 2, '2026-06-10', 'Urgent', 'Approved'),
(3, 'Sadia Akter', 'Chittagong General Hospital, Chittagong', 'O-', 1, '2026-06-15', 'Critical', 'Approved'),
(4, 'Rasel Mia', 'Sylhet MAG Osmani Medical College, Sylhet', 'B+', 3, '2026-06-20', 'Normal', 'Pending'),
(5, 'Momena Khatun', 'Rajshahi Medical College Hospital, Rajshahi', 'AB+', 1, '2026-06-12', 'Urgent', 'Approved');
