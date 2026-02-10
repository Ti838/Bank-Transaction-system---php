# API Documentation - TrustMora Bank

This document outlines the Application Programming Interfaces (APIs) and external services integrated into the TrustMora Bank system.

## 1. External Frontend APIs (CDNs)

The project utilizes several external libraries delivered via Content Delivery Networks (CDNs) for styling, icons, and interactive elements.

| Service | API/CDN URL | Purpose |
| :--- | :--- | :--- |
| **Tailwind CSS** | `https://cdn.tailwindcss.com` | Utility-first CSS framework for responsive design. |
| **Chart.js** | `https://cdn.jsdelivr.net/npm/chart.js` | Data visualization for dashboard statistics (Liquidity, Users, etc.). |
| **Vanilla Tilt** | `https://cdnjs.cloudflare.../vanilla-tilt.min.js` | 3D tilt effects for UI elements (Glassmorphism effect). |
| **Font Awesome** | `https://cdnjs.cloudflare.../all.min.css` | Icon library for UI elements (Dashboard icons, status indicators). |

## 2. Internal Banking APIs (Backend Logic)

While not exposed as public REST endpoints, the following core internal functions act as the "API surface" for banking operations within the system:

- **Auth API**: Managed via `login.php` and `signup.php`.
- **Transaction API**: Handled in `includes/functions.php`:
  - `process_deposit($account_id, $amount)`: Processes account credits.
  - `process_withdrawal($account_id, $amount)`: Processes account debits.
  - `process_transfer($from_id, $to_num, $amount)`: Atomic inter-account transfers.
- **Notification API**: `create_notification($user_id, $message)` - Internal messaging system.

## 3. Currency & Exchange Rates

Currently, the system uses a **Mock API** structure for currency conversion.

- **File**: `includes/functions.php` -> `get_exchange_rate()`
- **Status**: Manual/Hardcoded (e.g., USD to BDT = 110.50).
- **Potential Upgrade**: This can be connected to real-time APIs like *Fixer.io* or *ExchangeRate-API* in the future.

## 4. Database Connection

The system connects to a **MySQL** database via **PHP Data Objects (PDO)**.

- **Configuration**: `includes/db.php`
- **Security**: Uses prepared statements to prevent SQL injection.
