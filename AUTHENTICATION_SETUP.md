# Authentication Setup Summary

## ✅ Completed Changes

### 1. Database Configuration
- **Database**: PostgreSQL
- **Connection**: `pgsql` driver enabled in `php.ini`
- **Credentials**: 
  - Username: `postgres`
  - Password: `12345`
  - Database: `lta`
  - Port: `5432`

### 2. JWT Authentication Implementation
- **Package**: `php-open-source-saver/jwt-auth` v2.3
- **JWT Secret**: Generated and stored in `.env`
- **Token Contains**: User data embedded in custom claims
- **Auth Guard**: Changed from Sanctum to JWT (`auth:api`)

### 3. Security Features Implemented

#### ✅ Password Hashing
- Passwords are hashed using `Hash::make()` before storing
- User model has `'password' => 'hashed'` cast

#### ✅ Email Validation
- Required field validation
- Email format validation (`email` rule)
- Unique email validation (`unique:users,email`)

#### ✅ Registration
- **Does NOT return token** - users must login separately
- Returns success message and user object
- Validates: name, email, password (min 8 chars), password confirmation

#### ✅ Login
- Returns JWT token containing user data
- Token structure includes custom claims with user information
- Validates credentials before issuing token

#### ✅ Logout
- Invalidates JWT token on logout
- Uses `JWTAuth::invalidate()`

### 4. Password Reset Configuration

#### Email Configuration (`.env`)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=          # ⚠️ TO BE FILLED
MAIL_PASSWORD=          # ⚠️ TO BE FILLED
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Password Reset Features
- **Forgot Password**: Sends reset link via email
- **Reset Password**: Validates token and updates password
- **Custom URL**: Configured in `AppServiceProvider` to generate proper reset URLs
- **Token Expiry**: 60 minutes (configurable in `config/auth.php`)
- **Throttling**: 60 seconds between reset requests

## 📋 TODO: Email Configuration

To enable password reset emails, update `.env` with your email credentials:

### For Gmail:
1. Enable 2-Factor Authentication on your Google account
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Update `.env`:
```env
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### For Other SMTP Providers:
Update the SMTP settings accordingly:
```env
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🔑 API Endpoints

### Public Endpoints (No Authentication)
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login and get JWT token
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password with token

### Protected Endpoints (Require JWT Token)
- `POST /api/auth/logout` - Logout (invalidate token)
- All employee, attendance, and report endpoints

## 🔐 Using JWT Tokens

### Login Response
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### Making Authenticated Requests
Include the token in the Authorization header:
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Token Payload
The JWT token contains:
- User ID (subject)
- User data (custom claims)
- Expiration time
- Issued at time

## 🧪 Testing

### Test Registration
```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Test Login
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Test Protected Endpoint
```bash
curl -X GET http://127.0.0.1:8000/api/employees \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept: application/json"
```

## ⚠️ Important Notes

1. **Laravel Version**: Downgraded from 12.0 to 11.0 for JWT compatibility
2. **PostgreSQL Extensions**: `pdo_pgsql` and `pgsql` enabled in `php.ini`
3. **Migrations**: Run `php artisan migrate` to create database tables
4. **Server Restart**: Required after `php.ini` changes
5. **Email Testing**: Configure SMTP credentials before testing password reset

## 🔄 Next Steps

1. ✅ Fill in email credentials in `.env`
2. ✅ Test password reset flow
3. ✅ Test all authentication endpoints
4. ✅ Verify JWT token contains user data
5. ✅ Test protected endpoints with JWT authentication
