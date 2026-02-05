# 🚀 LTA Employee Manager API

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

A robust and modern **Employee Management System** built with the Laravel 11 framework. This API provides comprehensive employee lifecycle management, attendance tracking with automated notifications, and high-quality reporting in PDF and Excel formats.

---

## ✨ Core Features

### 🔐 Advanced Authentication
- **Stateless JWT Auth**: Implemented using `php-open-source-saver/jwt-auth` for secure, database-free token management.
- **Dual Token System**: Access and Refresh tokens for seamless session persistence.
- **OTP Recovery**: Secure 6-digit numeric OTP system for password resets.

### 👥 Employee Management
- **Full CRUD**: Comprehensive API endpoints for managing employee profiles.
- **Data Integrity**: Enforced validation rules and relational database constraints.

### 🕒 Attendance & Notifications
- **Smart Check-in/out**: Simple yet powerful attendance logging system.
- **Queued Alerts**: Asynchronous email notifications triggered on attendance events.
- **Automated Daily Reports**: Scheduled tasks to generate and distribute daily summaries.

### 📊 Professional Reporting
- **Multi-format Exports**: Generate attendance reports in high-fidelity **PDF** (`laravel-dompdf`) and **Excel** (`maatwebsite/excel`).
- **Customizable Views**: Tailored report layouts for business needs.

### 📖 Interactive Documentation
- **OpenAPI v3**: Fully documented API accessible via Swagger UI.
- **Real-time Testing**: Try out endpoints directly from the browser.

---

## 🛠️ Tech Stack

- **Backend**: [Laravel 11](https://laravel.com)
- **Database**: PostgreSQL (Recommended) / SQLite
- **Auth**: [JWT-Auth](https://jwt-auth-it.readthedocs.io/)
- **Reports**: [DOMPDF](https://github.com/barryvdh/laravel-dompdf) & [Laravel Excel](https://docs.laravel-excel.com/)
- **Docs**: [Swagger PHP](https://github.com/zircote/swagger-php)

---

## 🚀 Getting Started

### 1. Prerequisites
- **PHP 8.2** or higher
- **Composer**
- **PostgreSQL** (or any Laravel-supported database)
- **Mail Server** (Mailpit, Gmail SMTP, etc.)

### 2. Installation & Setup

```powershell
# Clone the repository
git clone <repository-url>
cd employee-manager

# Install PHP dependencies
composer install

# Configure Environment
copy .env.example .env

# Generate Application & JWT Secrets
php artisan key:generate
php artisan jwt:secret
```

### 3. Database Initialization

Configure your `DB_` credentials in `.env`, then run:
```powershell
php artisan migrate
```

### 4. Running the Application

Start the local development server:
```powershell
php artisan serve
```
*The API will be available at `http://127.0.0.1:8000`*

### 5. Background Tasks (Queues)

To handle email notifications and report generation in the background:
```powershell
php artisan queue:work
```

---

## 📂 API Reference

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/auth/login` | `POST` | Exchange credentials for JWT tokens |
| `/api/auth/refresh` | `POST` | Refresh expired access tokens |
| `/api/employees` | `GET/POST` | List or create employee records |
| `/api/employees/{id}` | `PUT/DELETE` | Update or remove an employee |
| `/api/attendance/check-in` | `POST` | Record attendance start |
| `/api/attendance/check-out` | `POST` | Record attendance end |
| `/api/reports/daily/pdf` | `GET` | Export daily report as PDF |
| `/api/reports/daily/excel` | `GET` | Export daily report as Excel |

Detailed documentation is available at:
👉 **`http://127.0.0.1:8000/api/docs`**

---

## 🛡️ Security
This project is configured with security in mind:
- Sensitive variables are restricted to `.env` (which is git-ignored).
- All requests are protected by the `auth:api` middleware.
- Input validation on all state-changing endpoints.

---

*Built with ❤️ by the **Liana Team Attendance (LTA)** Team.*
