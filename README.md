# Trust Mora Bank - Online Banking Transaction System

A complete mini banking transaction management system built with Flask, MySQL, and Tailwind CSS.

## Features

### Customer Features
- ✅ Account registration with auto-account creation
- ✅ Secure login/logout
- ✅ View account balance and details
- ✅ Deposit money
- ✅ Withdraw money (with balance validation)
- ✅ Transfer money to other accounts
- ✅ View complete transaction history
- ✅ Real-time notifications
- ✅ Update profile information

### Bank Official (Admin) Features
- ✅ System overview dashboard
- ✅ Manage all customer accounts
- ✅ Suspend/activate accounts
- ✅ Monitor all transactions
- ✅ View system statistics
- ✅ Access reports

### Staff/Teller Features
- ✅ Assist customers with transactions
- ✅ Process deposits and withdrawals
- ✅ View handled transactions

## Tech Stack

- **Backend**: Flask 3.0.0 (Python)
- **Database**: MySQL with SQLAlchemy ORM
- **Frontend**: Tailwind CSS
- **Authentication**: Flask-Login
- **Security**: Werkzeug password hashing, CSRF protection

## Installation

### 1. Prerequisites
- Python 3.8+
- XAMPP (for MySQL)
- Modern web browser

### 2. Install Dependencies
```bash
pip install -r requirements.txt
```

### 3. Setup Database

**Option A: Automatic (Recommended)**
1. Start XAMPP and run MySQL
2. Run the Flask app - database will be created automatically:
```bash
python main.py
```

**Option B: Manual**
1. Start XAMPP and run MySQL
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import `securebank.sql` file

### 4. Run Application
```bash
python main.py
```

Access at: **http://localhost:5000**

## Default Accounts

### Admin
- **Email**: admin@trustmorabank.com
- **Password**: admin123
- **Role**: Bank Official

### Sample Customers (if using securebank.sql)
- **Email**: john@example.com | **Password**: admin123
- **Email**: jane@example.com | **Password**: admin123

### Staff
- **Email**: staff@trustmorabank.com | **Password**: admin123

## Project Structure

```
PROJECT/
├── main.py                 # Flask application & routes
├── requirements.txt        # Python dependencies
├── securebank.sql         # Database schema with sample data
├── static/
│   └── favicon.png        # Bank favicon
└── templates/
    ├── base.html          # Base template with navigation
    ├── index.html         # Homepage
    ├── login.html         # Login page
    ├── signup.html        # Registration page
    ├── dashboard.html     # Customer dashboard
    ├── deposit.html       # Deposit form
    ├── withdraw.html      # Withdrawal form
    ├── transfer.html      # Transfer form
    ├── transactions.html  # Transaction history
    ├── profile.html       # User profile
    ├── admin_dashboard.html       # Admin overview
    ├── admin_accounts.html        # Account management
    ├── admin_transactions.html    # All transactions
    ├── admin_reports.html         # Reports page
    ├── staff_dashboard.html       # Staff homepage
    └── staff_assist.html          # Customer assistance

```

## Database Schema

### Tables
1. **user** - System users (Customer, BankOfficial, Staff)
2. **account** - Bank accounts with balances
3. **transaction** - Transaction records (Deposit, Withdrawal, Transfer)
4. **notification** - User notifications

## Usage

### For Customers
1. Sign up at `/signup`
2. Account automatically created with $0 balance
3. Login and navigate to dashboard
4. Use deposit/withdraw/transfer features
5. View transaction history

### For Bank Officials
1. Login with admin credentials
2. Access admin dashboard
3. Manage customer accounts
4. Monitor all transactions
5. Generate reports

### For Staff
1. Login with staff credentials
2. Use assist feature to help customers
3. Process deposits/withdrawals on behalf of customers

## Security Features

- Password hashing using Werkzeug
- CSRF protection on all forms
- Login required decorators
- Role-based access control
- SQL injection prevention via SQLAlchemy
- Session management

## Development

Built by: **Timon Biswas**

## License

All Rights Reserved - Proprietary Software

---

**Trust Mora Bank** - Your trusted online banking partner 🏦
