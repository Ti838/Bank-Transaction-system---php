-- Trust Mora Bank - 

-- Create database
DROP DATABASE IF EXISTS securebank;
CREATE DATABASE securebank;
USE securebank; 


-- Table: Roles

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


-- Table: Users (Core Authentication)

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(1000) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);


-- Table: Admin Details

CREATE TABLE admin_details (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    profile_picture VARCHAR(200),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Table: Staff Details

CREATE TABLE staff_details (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    bio TEXT,
    profile_picture VARCHAR(200),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Table: Customer Details

CREATE TABLE customer_details (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10),
    phone VARCHAR(20),
    address VARCHAR(200),
    bio TEXT,
    profile_picture VARCHAR(200),
    nominee_name VARCHAR(100),
    nominee_relationship VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Table: Accounts

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


-- Table: Transactions

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


-- Table: Notifications

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(20) DEFAULT 'Info',
    read_status BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Table: Settings (System Configuration)

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
);

-- Seed defaults settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('bank_name', 'Trust Mora Bank'), 
('transfer_fee', '10.00'), 
('maintenance_mode', '0'), 
('currency_symbol', '৳');


-- Views

CREATE VIEW user_roles_view AS
SELECT u.id AS user_id, u.email, r.name AS role_name, r.description AS role_description, u.created_at
FROM users u
JOIN roles r ON u.role_id = r.id;

CREATE VIEW view_customers AS
SELECT u.id, cd.full_name, u.email, cd.gender, cd.phone, cd.address, u.created_at 
FROM users u 
JOIN customer_details cd ON u.id = cd.user_id;

CREATE VIEW view_staff AS
SELECT u.id, sd.full_name, u.email, sd.phone, u.created_at 
FROM users u 
JOIN staff_details sd ON u.id = sd.user_id;

CREATE VIEW view_admins AS
SELECT u.id, ad.full_name, u.email, u.created_at 
FROM users u 
JOIN admin_details ad ON u.id = ad.user_id;

