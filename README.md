# Trust Mora Bank - Secure Digital Banking System

A premium, full-featured online banking management system with a modern glassmorphism aesthetic, built using PHP, MySQL, and Tailwind CSS.

## Features

### Customer Experience

- ✅ **Dynamic Dashboards**: Real-time balance and transaction activity charts (Chart.js).
- ✅ **Live Profiles**: Update personal metadata (Bio, Phone, Address, Gender) with instant DB reflection.
- ✅ **Secure Transactions**: Live Deposit, Withdrawal, and Transfers with automated protocol fees.
- ✅ **Account Lifecycle**: Self-signup followed by mandatory Admin approval protocol.
- ✅ **Notifications**: Instant system-wide operational alerts.
- ✅ **Glassmorphism UI**: High-end dark/light theme toggle with persistent storage.

### Admin (Bank Official) Command

- ✅ **Entity Control**: Global oversight of all users with Approve/Suspend/Delete protocols.
- ✅ **Intelligence Complex**: Advanced statistical reports, transaction trends, and volume charts.
- ✅ **System Analytics**: Export banking ledgers to CSV or prepare for physical print synchronization.
- ✅ **Global Configs**: Real-time management of Bank Name, Transfer Fees, and Maintenance Modes.

### Staff Resolution Core

- ✅ **Manual Intervention**: Assist subjects with direct deposit and withdrawal overrides.
- ✅ **Operational Logs**: Comprehensive audit trail of today's assisted resolutions.

## Tech Stack

- **Backend**: Core PHP 8.x
- **Database**: MySQL (Partitioned Architecture)
- **Frontend**: Tailwind CSS & Vanilla JavaScript
- **Visualization**: Chart.js
- **Design System**: Industrial Glassmorphism (Dark/Light)

## Installation

### 1. Prerequisites

- XAMPP/WAMPP (PHP 7.4+ & MySQL)
- Modern Web Browser

### 2. Database Setup

1. Start MySQL via XAMPP.
2. Open phpMyAdmin and create a database named `securebank`.
3. Import the `securebank.sql` file located in the root directory.
   - The script will automatically create the partitioned tables and seed default roles and settings.

### 3. Application Deployment

1. Move the project folder into your web server root (e.g., `htdocs`).
2. Point your browser to: **<http://localhost/TrustMora>**

## Project Structure

```
PROJECT/
├── php_version/            # Main application core
│   ├── admin/              # Administrator logistics
│   ├── customer/           # User interface
│   ├── staff/              # Staff resolution core
│   ├── includes/           # Functional logic (functions.php, db.php)
│   ├── templates/          # HTML view fragments (Glassmorphism based)
│   └── static/             # Assets (CSS, Icons, Uploads)
├── securebank.sql          # Partitioned database schema
└── README.md               # Documentation
```

## Database Partitioning

The system utilizing a simplified, high-performance data model:

1. **users** - Unified entity containing Authentication & Profile Data (Full Name, Address, Bio, etc.).
2. **roles** - Role definitions (Admin, Staff, Customer).
3. **accounts** - Financial vaults linked to Users.
4. **transactions** - Immutable operational ledger.
5. **notifications** - User alerts and messaging.
6. **settings** - Global system configuration parameters.

## Security Architecture

- **Role-Based Access Control (RBAC)**: Strict permission isolation.
- **Data Integrity**: Foreign key constraints with cascading logic.
- **Input Sanitization**: Prepared statements using PDO.
- **Session Security**: Multi-point validation and attempt limiting.

---
Built by: **Timon Biswas**
**Trust Mora Bank** - "Absolute Control. Absolute Security." 🏦
