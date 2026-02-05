# 🚀 How to Run and Test Your App

## ✅ App is Already Running!

Your Laravel API is running at: **http://127.0.0.1:8000**

The server was started with: `php artisan serve`

---

## 📝 Registered User

**Email:** raphaelnibishaka@gmail.com  
**Password:** password123

---

## 🧪 Testing the API

### Option 1: Using Postman (Recommended)

1. **Download Postman**: https://www.postman.com/downloads/

2. **Test Login:**
   - Method: `POST`
   - URL: `http://127.0.0.1:8000/api/auth/login`
   - Headers:
     - `Content-Type: application/json`
     - `Accept: application/json`
   - Body (raw JSON):
     ```json
     {
       "email": "raphaelnibishaka@gmail.com",
       "password": "password123"
     }
     ```
   - Click **Send**
   - You'll receive a JWT token in the response

3. **Test Protected Endpoints:**
   - Copy the token from login response
   - Method: `GET`
   - URL: `http://127.0.0.1:8000/api/employees`
   - Headers:
     - `Authorization: Bearer YOUR_JWT_TOKEN_HERE`
     - `Accept: application/json`
   - Click **Send**

### Option 2: Using Browser Extension

Install **Thunder Client** or **REST Client** extension in VS Code

### Option 3: Using PowerShell (Windows)

```powershell
# Login
$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/auth/login" `
  -Method Post `
  -ContentType "application/json" `
  -Body '{"email":"raphaelnibishaka@gmail.com","password":"password123"}'

# Show token
$response.token

# Use token for protected endpoint
$headers = @{
    "Authorization" = "Bearer $($response.token)"
    "Accept" = "application/json"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/employees" `
  -Method Get `
  -Headers $headers
```

---

## 📚 Available Endpoints

### Public Endpoints (No Authentication Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login and get JWT token |
| POST | `/api/auth/forgot-password` | Request password reset |
| POST | `/api/auth/reset-password` | Reset password with token |

### Protected Endpoints (Require JWT Token)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/logout` | Logout (invalidate token) |
| GET | `/api/employees` | List all employees |
| POST | `/api/employees` | Create employee |
| GET | `/api/employees/{id}` | Get employee details |
| PUT | `/api/employees/{id}` | Update employee |
| DELETE | `/api/employees/{id}` | Delete employee |
| GET | `/api/attendance` | List attendance records |
| POST | `/api/attendance/check-in` | Check in |
| POST | `/api/attendance/check-out` | Check out |
| GET | `/api/reports/attendance/daily/pdf` | Download daily PDF report |
| GET | `/api/reports/attendance/daily/excel` | Download daily Excel report |

---

## 🔑 How JWT Authentication Works

1. **Login** → Receive JWT token
2. **Store token** (in your app/frontend)
3. **Include token** in `Authorization: Bearer {token}` header for all protected requests
4. **Token contains user data** - no need to fetch user separately!

### Decode Your JWT Token

Visit: https://jwt.io

Paste your token to see the user data embedded inside!

---

## 🛠️ Useful Commands

```bash
# Start the server (if not running)
php artisan serve

# View logs
php artisan pail

# Clear cache
php artisan cache:clear
php artisan config:clear

# Run migrations
php artisan migrate

# Create a new user
php register_user.php
```

---

## 📧 Email Configuration (For Password Reset)

To enable password reset emails, update `.env`:

```env
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

For Gmail:
1. Enable 2FA: https://myaccount.google.com/security
2. Create App Password: https://myaccount.google.com/apppasswords
3. Use the app password in `.env`

---

## 🎯 Quick Start Example

1. **Open Postman**
2. **Login:**
   ```
   POST http://127.0.0.1:8000/api/auth/login
   Body: {"email":"raphaelnibishaka@gmail.com","password":"password123"}
   ```
3. **Copy the token from response**
4. **Test protected endpoint:**
   ```
   GET http://127.0.0.1:8000/api/employees
   Header: Authorization: Bearer YOUR_TOKEN
   ```

---

## 🐛 Troubleshooting

**Server not running?**
```bash
php artisan serve
```

**Database errors?**
```bash
php artisan migrate
```

**Cache issues?**
```bash
php artisan config:clear
php artisan cache:clear
```

**Need to restart server?**
- Press `Ctrl+C` in the terminal running `php artisan serve`
- Run `php artisan serve` again

---

## 📖 API Documentation

View OpenAPI/Swagger documentation:
- JSON: http://127.0.0.1:8000/api/openapi.json
- Import this into Swagger UI or Postman for interactive docs

---

**Happy Testing! 🎉**
