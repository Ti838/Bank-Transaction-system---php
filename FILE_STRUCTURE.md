# System Architecture & File Structure Documentation

This document provides a detailed overview of the **Trust Mora Bank** project structure, explaining the purpose of each file and how they are interconnected.

## 📂 Root Directory

- **`index.php`**: The entry point. Redirects users to `php_version/index.php`.
- **`securebank.sql`**: The database schema file. Import this into MySQL to create the `securebank` database and its tables (`users`, `roles`, `accounts`, etc.).
- **`README.md`**: General project documentation.
- **`LICENSE`**: Proprietary license information.

## 📂 `php_version/` (Core Application)

- **`index.php`**: The landing page. Displays the "Trust Mora Bank" marketing page.
- **`signup.php`**: User registration logic.
  - *Connects to*: `users` table, `roles` table, `accounts` table.
- **`login.php`**: User authentication logic.
  - *Connects to*: `users` table.
  - *Redirects to*: Role-specific dashboards.
- **`logout.php`**: Destroys the session and redirects to login.

### 📂 `includes/` (Helper Logic)

- **`db.php`**: establishing the PDO connection to the MySQL database.
  - *Used by*: All PHP files requiring database access.
- **`functions.php`**: Contains helper functions (`render`, `create_notification`, `redirect`, `process_deposit`, etc.).
  - *Used by*: Almost every controller file.
- **`header.php`**: Common HTML head, styles, and scripts.
- **`navbar.php`**: The navigation bar. Logic changes based on user role (Admin/Staff/Customer).
- **`footer.php`**: Common footer.

### 📂 `admin/` (Administrator Panel)

- **`dashboard.php`**: Main Admin view. Shows stats (Liquidity, User count).
  - *Connects to*: `accounts`, `users`, `transactions` tables.
- **`users.php`**: Manage users (Approve, Suspend, Delete, Promote).
  - *Connects to*: `users`, `roles` tables.
- **`reports.php`**: View and export system-wide transaction reports.
  - *Connects to*: `transactions`, `users` tables.
- **`accounts.php`**: View all bank accounts.
- **`transactions.php`**: View global transaction ledger.
- **`credit.php`**: Inject money into an account (Bank Credit).
- **`settings.php`**: Manage system settings (Fees, Maintenance Mode).

### 📂 `staff/` (Staff Panel)

- **`dashboard.php`**: Staff main view.
- **`assist.php`**: Perform deposits/withdrawals on behalf of customers.
- **`reports.php`**: View daily transaction logs for the staff member.

### 📂 `customer/` (Customer Panel)

- **`dashboard.php`**: Customer main view. Shows balance and recent activity.
  - *Connects to*: `accounts`, `transactions` tables.
- **`profile.php`**: Update personal info (Picture, Bio, Password).
  - *Connects to*: `users` table.
- **`transfer.php`**: Send money to other accounts.
- **`deposit.php`**: Request a deposit.
- **`withdraw.php`**: Request a withdrawal.
- **`transactions.php`**: View personal transaction history.
- **`receipt.php`**: View detailed receipt for a specific transaction.

### 📂 `templates/` (HTML Views)

Contains the `.html` files for the UI. These are loaded by the PHP controllers using the `render()` function.

- **`root/`**: Login, Signup, Landing views.
- **`admin/`**: Admin dashboard views.
- **`staff/`**: Staff dashboard views.
- **`customer/`**: Customer dashboard views.
- **`shared/`**: Error pages, success pages.

### 📂 `static/`

- **`uploads/profiles/`**: Stores user profile pictures.
- **`css/`**: Custom stylesheets (if any).
- **`js/`**: Custom JavaScript files.
- **`favicon.png`**: Site icon.

---

## 🔗 Key Connections

1. **Authentication Flow**:
    `index.php` -> `login.php` -> `(Check Role)` -> `admin/dashboard.php` OR `staff/dashboard.php` OR `customer/dashboard.php`.

2. **Database Connection**:
    Every PHP file starts with `require_once 'includes/functions.php'`, which in turn includes `includes/db.php`.

3. **UI Rendering**:
    Controller (e.g., `admin/users.php`) fetches data -> Calls `render('admin/users', $data)` -> Loads `templates/admin/users.html`.
