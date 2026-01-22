# Trust Mora Bank Database Setup Guide

## Quick Start (Recommended)

### Step 1: Start XAMPP MySQL

1. Open XAMPP Control Panel
2. Click **Start** next to MySQL
3. Wait for green status

### Step 2: Run the Application

```bash
cd "c:\Users\TIMON\Desktop\Bank-Transaction-system-main\python_version"
python main.py
```

**That's it!** The database will be created automatically.

---

## Manual Setup (Optional)

If you prefer to import the SQL file manually:

### Step 1: Start XAMPP MySQL

Same as above

### Step 2: Import Database

1. Open phpMyAdmin: <http://localhost/phpmyadmin>
2. Click **Import** tab
3. Click **Choose File**
4. Select `securebank.sql`
5. Click **Go**

### Step 3: Run Application

```bash
python main.py
```

---

## Database Details

**Database Name:** `securebank`

**Tables:**

- `user` - All system users
- `account` - Bank accounts
- `transaction` - All transactions
- `notification` - User notifications

**Sample Accounts:**

| Email | Password | Role | Account # |
|-------|----------|------|-----------|
| <admin@trustmorabank.com> | admin123 | Bank Official | - |
| <john@example.com> | admin123 | Customer | 1234567890 |
| <jane@example.com> | admin123 | Customer | 9876543210 |
| <staff@trustmorabank.com> | admin123 | Staff | - |

---

## Troubleshooting

**"Can't connect to MySQL server"**

- Check XAMPP - MySQL must be **green/running**
- Restart XAMPP if needed

**"Database securebank doesn't exist"**

- No problem! Flask creates it automatically
- Or import `securebank.sql` manually

**"Table doesn't exist"**

- Flask creates all tables via `db.create_all()`
- Just run `python main.py`

---

## Testing

1. Start application
2. Go to <http://localhost:5000>
3. Click **Sign Up** or use sample accounts above
4. Test deposits, withdrawals, transfers!

---

**Need help?** Check README.md for full documentation.
