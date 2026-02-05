# 🚀 LTA Employee Manager API

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

A professional, industrial-grade **Employee Management & Attendance System** built with **Laravel 11**. This system provides a stateless API for managing employee lifecycles, tracking attendance with real-time notifications, and generating automated daily reports in multiple formats.

---

## ✨ Core Features

### 🔐 Enterprise-Grade Authentication
- **Stateless JWT Auth**: Powered by `php-open-source-saver/jwt-auth`. High-performance, database-less token verification.
- **Custom Claims**: Tokens include user profile data (ID, Name, Email) directly in the payload.
- **Secure Recovery**: 6-digit numeric OTP system for secure password resets via email.

### 👥 Comprehensive Employee Management
- **Full CRUD Operations**: Manage employee profiles with strictly validated endpoints.
- **Resourceful Routing**: Clean, RESTful API design.

### 🕒 Intelligent Attendance Tracking
- **Check-in/Check-out**: Precise timestamping for employee attendance.
- **Real-time Notifications**: Automated email alerts sent to employees upon attendance recording.
- **Queued Execution**: All emails are processed in background workers to ensure zero latency for the user.

### 📊 Automated Reporting & Analytics
- **Multi-Format Exports**: High-fidelity reports in **PDF** (via DOMPDF) and **Excel** (via Maatwebsite/Excel).
- **Daily Summaries**: Automated system that compiles attendance data and distributes reports to management.

### 📖 Living Documentation
- **Swagger/OpenAPI Support**: Interactive API documentation available at `/api/docs`.
- **Zero-Config Testing**: Test endpoints directly from the browser documentation.

---

## 🛠️ Tech Stack

- **Kernel**: [Laravel 11](https://laravel.com)
- **Runtime**: PHP 8.2+
- **Database**: PostgreSQL (Recommended)
- **Auth Engine**: [JWT-Auth (Open Source)](https://jwt-auth-it.readthedocs.io/)
- **Reports**: [DOMPDF](https://github.com/barryvdh/laravel-dompdf) & [Laravel Excel](https://docs.laravel-excel.com/)
- **Docs Engine**: [Swagger PHP](https://github.com/zircote/swagger-php)

---

## 🏁 Installation & Setup Guide

Follow these steps precisely to set up the development environment.

### Step 1: Clone & Initialize
```powershell
# Clone the repository
git clone https://github.com/TETA-Liana/employee-manager.git
cd employee-manager

# Install PHP dependencies
composer install
```

### Step 2: Environment Configuration
```powershell
# Create environment file from template
copy .env.example .env
```
Open `.env` and configure your database and mail credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=lta
DB_USERNAME=postgres
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### Step 3: Secrets & Database
```powershell
# Generate application key
php artisan key:generate

# Generate JWT secret key
php artisan jwt:secret

# Run database migrations
php artisan migrate
```

### Step 4: Start Services
To run the full system, you need three services active:

1. **API Server**:
   ```powershell
   php artisan serve
   ```
2. **Background Workers (for Emails)**:
   ```powershell
   php artisan queue:work
   ```
3. **Scheduled Tasks (for Daily Reports)**:
   ```powershell
   php artisan schedule:work
   ```

---

## 🧪 Testing the API

### 1. Register/Login
First, register a user or use the built-in registration script:
```bash
php register_user.php
```

Then, obtain a JWT token:
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"raphaelnibishaka@gmail.com", "password":"password123"}'
```

### 2. Access Protected Data
Use the token in the `Authorization` header:
```bash
curl -X GET http://127.0.0.1:8000/api/employees \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 📂 API Reference

| Category | Method | Endpoint | Description |
| :--- | :--- | :--- | :--- |
| **Auth** | `POST` | `/api/auth/login` | Login & receive JWT |
| | `POST` | `/api/auth/register` | Create new account |
| **Employees** | `GET` | `/api/employees` | List all employees |
| | `POST` | `/api/employees` | Hire new employee |
| | `PUT` | `/api/employees/{id}` | Update details |
| **Attendance**| `POST` | `/api/attendance/check-in` | Record start time |
| | `POST` | `/api/attendance/check-out`| Record end time |
| **Reports** | `GET` | `/api/reports/daily/pdf` | Export PDF |
| | `GET` | `/api/reports/daily/excel` | Export Excel |

---

## 📁 Project Structure

*   `app/Http/Controllers/`: API Logic and request handling.
*   `app/Models/`: Database schemas and relationships.
*   `app/Mail/`: Email templates and logic (Attendance & Reports).
*   `app/Console/Commands/`: Scheduled tasks for report generation.
*   `routes/api.php`: All API endpoint definitions.
*   `resources/views/`: PDF and Email HTML templates.

---

## 🐛 Troubleshooting

*   **Database Connection Refused**: Ensure PostgreSQL service is running and credentials in `.env` match.
*   **JWT Secret Error**: Run `php artisan jwt:secret` if you get a "Secret not provided" error.
*   **Emails not sending**: Check `MAIL_` settings in `.env` and ensure `queue:work` is running.
*   **PDF errors**: Ensure the `storage/` directory has write permissions.

---

*Built with ❤️ by the **Liana Team Attendance (LTA)** Team.*
