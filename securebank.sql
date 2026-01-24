-- 1. Create Database
DROP DATABASE IF EXISTS securebank;
CREATE DATABASE securebank;
USE securebank;

-- 2. Create Roles Table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Insert Default Roles
INSERT INTO roles (name) VALUES 
('Admin'), 
('Staff'), 
('Customer');

-- 4. Create Users Table (Unified)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(200),
    bio TEXT,
    profile_picture VARCHAR(200),
    gender VARCHAR(10),
    nominee_name VARCHAR(100),
    nominee_relationship VARCHAR(50),
    kyc_document VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- 5. Create Accounts Table
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    account_number VARCHAR(10) NOT NULL UNIQUE,
    account_type VARCHAR(20) DEFAULT 'Savings',
    balance DECIMAL(15,2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'Active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. Create Transactions Table
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_type VARCHAR(20) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    fee DECIMAL(15,2) DEFAULT 0.00,
    from_account_id INT,
    to_account_id INT,
    status VARCHAR(20) DEFAULT 'Success',
    description VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_account_id) REFERENCES accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (to_account_id) REFERENCES accounts(id) ON DELETE SET NULL
);

-- 7. Create Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(20) DEFAULT 'Info',
    read_status BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. Create Settings Table
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
);

-- 9. Insert Default Settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('bank_name', 'Trust Mora Bank'), 
('transfer_fee', '10.00'), 
('maintenance_mode', '0'), 
('currency_symbol', '৳');

-- 10. Seed Data (System Admin & Reserve)
-- Password for Admin is 'admin123' (hash: $2y$10$...)
INSERT INTO users (role_id, email, password_hash, full_name, bio) VALUES 
(1, 'admin@trustmora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'Central Command');

-- System Reserve Account (Must be ID 1 or account number 2020000001)
INSERT INTO accounts (user_id, account_number, account_type, balance, status) VALUES 
(1, '2020000001', 'Savings', 1000000000.00, 'Active');
