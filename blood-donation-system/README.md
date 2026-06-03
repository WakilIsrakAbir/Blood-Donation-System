# 🩸 BloodConnect - Blood Donation Management System

A full-stack web application for managing blood donations, connecting donors with patients in need. Built with PHP, MySQL, HTML, CSS, and JavaScript.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 📋 Features

### Public Pages
- **Home Page** — Hero section, live stats, recent blood requests
- **Search Donors** — Filter by blood group & district (contact info hidden for guests)
- **About Us** — Mission, eligibility criteria, FAQ
- **Contact Us** — Contact form (saved to database)

### Authentication
- User Registration with password hashing (bcrypt)
- Role-based Login (Admin/User redirect)
- Session management with `session_regenerate_id()`
- Banned user access prevention

### User Panel
- **Dashboard** — Stats overview, donation eligibility, quick actions
- **Post Blood Request** — Submit requests (pending admin approval)
- **Available Requests Feed** — View approved requests, pledge to donate
- **90-Day Donation Rule** — System enforces minimum 90-day gap between donations
- **Self-Block** — Users cannot donate to their own requests
- **Profile Management** — Update details, last donation date, password
- **Donation History** — Track all pledged/completed donations

### Admin Panel
- **Dashboard** — System-wide stats, blood group distribution, recent activity
- **Manage Users** — Search, ban/unban, delete users
- **Manage Blood Requests** — Approve/Reject pending requests (moderation)
- **Manage Donations** — Mark pledges as completed (auto-updates donor's last donation date)
- **Contact Messages** — Read/delete contact form submissions

### Security
- ✅ PDO Prepared Statements (SQL Injection Prevention)
- ✅ Password Hashing with `password_hash()` / `password_verify()`
- ✅ CSRF Token Protection on all forms
- ✅ XSS Prevention with `htmlspecialchars()`
- ✅ Session-based Access Control (role checking on every protected page)
- ✅ Input Validation (client-side + server-side)

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8.0+ |
| Database | MySQL 5.7+ |
| Server | Apache (XAMPP/WAMP/LAMP) |

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache Web Server (XAMPP, WAMP, MAMP, or LAMP)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/blood-donation-system.git
   ```

2. **Move to your web server directory**
   ```bash
   # For XAMPP
   cp -r blood-donation-system /path/to/xampp/htdocs/

   # For WAMP
   cp -r blood-donation-system /path/to/wamp/www/
   ```

3. **Create the database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `blood_donation_db`
   - Import the SQL file: `database/blood_donation.sql`

   Or via command line:
   ```bash
   mysql -u root -p < database/blood_donation.sql
   ```

4. **Configure database connection**
   - Open `config/db.php`
   - Update the credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'blood_donation_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

5. **Start your server and visit**
   ```
   http://localhost/blood-donation-system/
   ```

### Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@blooddonation.com | password123 |
| User | rahim@example.com | password123 |
| User | karim@example.com | password123 |

## 📁 Project Structure

```
blood-donation-system/
├── config/           # Database & session configuration
├── includes/         # Header, footer, utility functions
├── assets/
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   └── images/       # Static assets
├── user/             # User panel pages (protected)
├── admin/            # Admin panel pages (protected)
├── database/         # SQL schema & seed data
├── index.php         # Home page
├── search_donors.php # Public donor search
├── about.php         # About page
├── contact.php       # Contact form
├── register.php      # User registration
├── login.php         # User login
├── logout.php        # Session destroy
└── README.md         # This file
```

## 📊 Database Schema

### Tables
- **users** — Stores all registered users (donors & admins)
- **blood_requests** — Blood requests posted by users
- **donations** — Tracks who pledged/donated for which request
- **contact_messages** — Contact form submissions

### ER Diagram
`users` → `blood_requests` (1:N)
`users` → `donations` (1:N)
`blood_requests` → `donations` (1:N)

## 📝 License

This project is developed as part of the CSE472 Web Technologies course assignment.

## 👤 Author

- **Name**: [Your Name]
- **Student ID**: [Your ID]
- **Course**: CSE472 - Web Technologies
