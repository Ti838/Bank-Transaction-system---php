-- Trust Mora Bank - Bulk Import (Bengali Names Edition)
-- This script CLEARS ALL EXISTING DATA and inserts 50 new users with Bengali names.
-- Roles: 1=Admin, 2=Staff, 3=Customer

-- 1. CLEANUP (DELETE OLD DATA)
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM transactions;
ALTER TABLE transactions AUTO_INCREMENT = 1;

DELETE FROM notifications;
ALTER TABLE notifications AUTO_INCREMENT = 1;

DELETE FROM accounts;
ALTER TABLE accounts AUTO_INCREMENT = 1;

DELETE FROM users;
ALTER TABLE users AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- 2. INSERT USERS (Role 1: Admin - 10 Users)
INSERT INTO users (role_id, email, password_hash, full_name, phone, address, gender, bio) VALUES 
(1, 'admin@trustmora.com', 'password123', 'Rahim Uddin', '01700000001', 'Dhaka, HQ', 'Male', 'Head of IT'),
(1, 'kamal@trustmora.com', 'password123', 'Kamal Hossain', '01700000002', 'Dhaka', 'Male', 'Senior DB Admin'),
(1, 'nasrin@trustmora.com', 'password123', 'Nasrin Akter', '01700000003', 'Chittagong', 'Female', 'Security Lead'),
(1, 'jamal@trustmora.com', 'password123', 'Jamal Uddin', '01700000004', 'Sylhet', 'Male', 'Network Admin'),
(1, 'salma@trustmora.com', 'password123', 'Salma Begum', '01700000005', 'Rajshahi', 'Female', 'Audit Admin'),
(1, 'rafiq@trustmora.com', 'password123', 'Rafiqul Islam', '01700000006', 'Dhaka', 'Male', 'SysOps'),
(1, 'farhana@trustmora.com', 'password123', 'Farhana Yasmin', '01700000007', 'Khulna', 'Female', 'Access Control'),
(1, 'ashraf@trustmora.com', 'password123', 'Ashraf Ali', '01700000008', 'Barisal', 'Male', 'Server Lead'),
(1, 'momena@trustmora.com', 'password123', 'Momena Khatun', '01700000009', 'Comilla', 'Female', 'Logistics'),
(1, 'sohel@trustmora.com', 'password123', 'Sohel Rana', '01700000010', 'Dhaka', 'Male', 'Chief Admin');

-- 3. INSERT USERS (Role 2: Staff - 10 Users)
INSERT INTO users (role_id, email, password_hash, full_name, phone, address, gender, bio) VALUES 
(2, 'staff1@trustmora.com', 'password123', 'Abul Kalam', '01800000001', 'Gulshan Branch', 'Male', 'Teller'),
(2, 'staff2@trustmora.com', 'password123', 'Fatema Begum', '01800000002', 'Banani', 'Female', 'CS Manager'),
(2, 'staff3@trustmora.com', 'password123', 'Hassan Mahmud', '01800000003', 'Uttara', 'Male', 'Loan Officer'),
(2, 'staff4@trustmora.com', 'password123', 'Rina Parvin', '01800000004', 'Mirpur', 'Female', 'Front Desk'),
(2, 'staff5@trustmora.com', 'password123', 'Sajedul Islam', '01800000005', 'Dhanmondi', 'Male', 'Cashier'),
(2, 'staff6@trustmora.com', 'password123', 'Tasnim Jara', '01800000006', 'Motijheel', 'Female', 'Support'),
(2, 'staff7@trustmora.com', 'password123', 'Mizanur Rahman', '01800000007', 'Savar', 'Male', 'Accountant'),
(2, 'staff8@trustmora.com', 'password123', 'Sharmin Sultana', '01800000008', 'Gazipur', 'Female', 'Teller'),
(2, 'staff9@trustmora.com', 'password123', 'Babul Mia', '01800000009', 'Narayanganj', 'Male', 'Guard'),
(2, 'staff10@trustmora.com', 'password123', 'Jannatul Ferdous', '01800000010', 'Tangail', 'Female', 'Intern');

-- 4. INSERT USERS (Role 3: Customer - 30 Users)
INSERT INTO users (role_id, email, password_hash, full_name, phone, address, gender, bio) VALUES 
(3, 'user1@trustmora.com', 'password123', 'Sakib Al Hasan', '01900000001', 'Magura', 'Male', 'Cricketer'),
(3, 'user2@trustmora.com', 'password123', 'Tamim Iqbal', '01900000002', 'Chittagong', 'Male', 'Businessman'),
(3, 'user3@trustmora.com', 'password123', 'Mashrafe Mortaza', '01900000003', 'Narail', 'Male', 'MP'),
(3, 'user4@trustmora.com', 'password123', 'Mushfiqur Rahim', '01900000004', 'Bogura', 'Male', 'Sports'),
(3, 'user5@trustmora.com', 'password123', 'Mahmudullah Riyad', '01900000005', 'Mymensingh', 'Male', 'Sports'),
(3, 'user6@trustmora.com', 'password123', 'Taskin Ahmed', '01900000006', 'Dhaka', 'Male', 'Pacer'),
(3, 'user7@trustmora.com', 'password123', 'Mustafizur Rahman', '01900000007', 'Satkhira', 'Male', 'Fizz'),
(3, 'user8@trustmora.com', 'password123', 'Liton Das', '01900000008', 'Dinajpur', 'Male', 'Keeper'),
(3, 'user9@trustmora.com', 'password123', 'Mehidy Hasan', '01900000009', 'Khulna', 'Male', 'Miraz'),
(3, 'user10@trustmora.com', 'password123', 'Soumya Sarkar', '01900000010', 'Satkhira', 'Male', 'Batsman'),
(3, 'user11@trustmora.com', 'password123', 'Jahanara Alam', '01900000011', 'Dhaka', 'Female', 'Model'),
(3, 'user12@trustmora.com', 'password123', 'Salma Khatun', '01900000012', 'Khulna', 'Female', 'Captain'),
(3, 'user13@trustmora.com', 'password123', 'Rumana Ahmed', '01900000013', 'Bogra', 'Female', 'All Rounder'),
(3, 'user14@trustmora.com', 'password123', 'Nigar Sultana', '01900000014', 'Dhaka', 'Female', 'Batter'),
(3, 'user15@trustmora.com', 'password123', 'Fargana Hoque', '01900000015', 'Gaibandha', 'Female', 'Pinky'),
(3, 'user16@trustmora.com', 'password123', 'Sanida Islam', '01900000016', 'Rangpur', 'Female', 'Student'),
(3, 'user17@trustmora.com', 'password123', 'Fahima Khatun', '01900000017', 'Barisal', 'Female', 'Bowler'),
(3, 'user18@trustmora.com', 'password123', 'Shamima Sultana', '01900000018', 'Faridpur', 'Female', 'Teacher'),
(3, 'user19@trustmora.com', 'password123', 'Nahida Akter', '01900000019', 'Dhaka', 'Female', 'Doctor'),
(3, 'user20@trustmora.com', 'password123', 'Ritu Moni', '01900000020', 'Bogra', 'Female', 'Engineer'),
(3, 'user21@trustmora.com', 'password123', 'Anisul Hoque', '01900000021', 'Dhaka', 'Male', 'Writer'),
(3, 'user22@trustmora.com', 'password123', 'Humayun Ahmed', '01900000022', 'Netrokona', 'Male', 'Author'),
(3, 'user23@trustmora.com', 'password123', 'Zafar Iqbal', '01900000023', 'Sylhet', 'Male', 'Professor'),
(3, 'user24@trustmora.com', 'password123', 'Imdadul Haq Milan', '01900000024', 'Dhaka', 'Male', 'Media'),
(3, 'user25@trustmora.com', 'password123', 'Rabindranath Tagore', '01900000025', 'Kushtia', 'Male', 'Poet'),
(3, 'user26@trustmora.com', 'password123', 'Kazi Nazrul Islam', '01900000026', 'Churulia', 'Male', 'Rebel Poet'),
(3, 'user27@trustmora.com', 'password123', 'Jibanananda Das', '01900000027', 'Barisal', 'Male', 'Banalata'),
(3, 'user28@trustmora.com', 'password123', 'Begum Rokeya', '01900000028', 'Rangpur', 'Female', 'Pioneer'),
(3, 'user29@trustmora.com', 'password123', 'Sufia Kamal', '01900000029', 'Barisal', 'Female', 'Activist'),
(3, 'user30@trustmora.com', 'password123', 'Jahanara Imam', '01900000030', 'Dhaka', 'Female', 'Shaheed Janani');

-- 5. CREATE ACCOUNTS FOR ALL USERS (Loop simulation via Insert-Select or manual)
-- Because we need specific Balances, we will insert individually based on IDs assuming auto-increment works sequentially.
-- IDs 1-10 (Admin), 11-20 (Staff), 21-50 (Customer)

-- Admins (Accounts)
INSERT INTO accounts (user_id, account_number, account_type, balance, status) SELECT id, CONCAT('2020', LPAD(id, 6, '0')), 'Savings', 10000000.00, 'Active' FROM users WHERE role_id = 1;

-- Staff (Accounts)
INSERT INTO accounts (user_id, account_number, account_type, balance, status) SELECT id, CONCAT('2020', LPAD(id, 6, '0')), 'Savings', 50000.00, 'Active' FROM users WHERE role_id = 2;

-- Customers (Accounts)
INSERT INTO accounts (user_id, account_number, account_type, balance, status) SELECT id, CONCAT('2020', LPAD(id, 6, '0')), 'Savings', FLOOR(RAND() * 500000), 'Active' FROM users WHERE role_id = 3;

-- 6. ADD INITIAL TRANSACTIONS FOR CHARTS (Quick Batch)
INSERT INTO transactions (transaction_type, amount, to_account_id, status, description, created_at)
SELECT 'Deposit', 50000, id, 'Success', 'Initial Deposit', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 10) DAY)
FROM accounts;


-- ==========================================
-- 7. SYNTHETIC HISTORY (LAST 7 DAYS)
-- ==========================================
-- Day 1 (7 Days Ago)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Deposit', 50000.00, 0.00, NULL, 2, 'Success', 'Opening Deposit', DATE_SUB(NOW(), INTERVAL 6 DAY)),
('Deposit', 20000.00, 0.00, NULL, 3, 'Success', 'Salary Inflow', DATE_SUB(NOW(), INTERVAL 6 DAY)),
('Transfer', 5000.00, 10.00, 2, 3, 'Success', 'Payment for Service', DATE_SUB(NOW(), INTERVAL 6 DAY));

-- Day 2 (6 Days Ago)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Withdrawal', 2000.00, 0.00, 2, NULL, 'Success', 'ATM Withdrawal', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Deposit', 100000.00, 0.00, NULL, 4, 'Success', 'Investment', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Transfer', 15000.00, 10.00, 4, 1, 'Success', 'Reserve Transfer', DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Day 3 (5 Days Ago)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Deposit', 10000.00, 0.00, NULL, 5, 'Success', 'Cash Deposit', DATE_SUB(NOW(), INTERVAL 4 DAY)),
('Deposit', 3000.00, 0.00, NULL, 6, 'Success', 'Mobile Banking', DATE_SUB(NOW(), INTERVAL 4 DAY)),
('Transfer', 25000.00, 10.00, 1, 2, 'Success', 'Loan Disbursement', DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Day 4 (3 Days Ago)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Withdrawal', 500.00, 0.00, 3, NULL, 'Success', 'Snacks', DATE_SUB(NOW(), INTERVAL 3 DAY)),
('Transfer', 12000.00, 10.00, 3, 5, 'Success', 'Rent Payment', DATE_SUB(NOW(), INTERVAL 3 DAY)),
('Deposit', 75000.00, 0.00, NULL, 1, 'Success', 'Govt Grant', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Day 5 (2 Days Ago)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Deposit', 500.00, 0.00, NULL, 7, 'Success', 'Gift', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Transfer', 2000.00, 10.00, 7, 8, 'Success', 'Dinner Share', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Withdrawal', 10000.00, 0.00, 6, NULL, 'Success', 'Emergency', DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Day 6 (Yesterday)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Deposit', 90000.00, 0.00, NULL, 9, 'Success', 'Project Fee', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Transfer', 45000.00, 10.00, 9, 10, 'Success', 'Vendor Payment', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Transfer', 500.00, 10.00, 2, 4, 'Success', 'Tip', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Day 7 (Today)
INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description, created_at) VALUES 
('Deposit', 150000.00, 0.00, NULL, 10, 'Success', 'Large Contract', NOW()),
('Withdrawal', 50000.00, 0.00, 9, NULL, 'Success', 'Equipment Purchase', NOW()),
('Transfer', 5000.00, 10.00, 1, 5, 'Success', 'Bonus Payout', NOW());

-- Backdate some Users creation time for "Growth Chart"
UPDATE users SET created_at = DATE_SUB(NOW(), INTERVAL 5 DAY) WHERE id BETWEEN 2 AND 10;
UPDATE users SET created_at = DATE_SUB(NOW(), INTERVAL 3 DAY) WHERE id BETWEEN 11 AND 20;
UPDATE users SET created_at = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id BETWEEN 21 AND 30;
