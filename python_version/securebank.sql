-- =============================================
-- Trust Mora Bank - 
-- =============================================

-- Create database
DROP DATABASE IF EXISTS securebank;
CREATE DATABASE securebank;
USE securebank;

-- =============================================
-- Table: Roles
-- =============================================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert predefined roles
INSERT INTO roles (name) VALUES
('Admin'),
('Staff'),
('Customer');

-- =============================================
-- Table: Users
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(1000) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    usertype VARCHAR(50) NOT NULL,
    gender VARCHAR(10),
    phone VARCHAR(20),
    address VARCHAR(200),
    bio TEXT,
    profile_picture VARCHAR(200),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- =============================================
-- Table: Accounts
-- =============================================
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    account_number VARCHAR(10) NOT NULL UNIQUE,
    account_type VARCHAR(20) DEFAULT 'Savings',
    balance DECIMAL(15,2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'Active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =============================================
-- Table: Transactions
-- =============================================
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

-- =============================================
-- Table: Notifications
-- =============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(20) DEFAULT 'Info',
    read_status BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =============================================
-- Views
-- =============================================
CREATE VIEW user_roles_view AS
SELECT u.id AS user_id, u.full_name, u.email, r.name AS role_name, r.description AS role_description, u.created_at
FROM users u
JOIN roles r ON u.role_id = r.id;

CREATE VIEW view_customers AS
SELECT id, full_name, email, gender, phone, address, created_at 
FROM users WHERE usertype = 'Customer';

CREATE VIEW view_staff AS
SELECT id, full_name, email, phone, created_at 
FROM users WHERE usertype = 'Staff';

CREATE VIEW view_admins AS
SELECT id, full_name, email, created_at 
FROM users WHERE usertype = 'Admin';
