# 📧 Email Configuration Guide

## ✅ What's Been Set Up

Your registration now:
1. ✅ Creates a new user account
2. ✅ Generates a JWT token for the user
3. ✅ Sends a welcome email with the token to the user's email address

## 🔧 Configure Email Credentials

### Step 1: Get Gmail App Password

1. **Go to Google Account Security**: https://myaccount.google.com/security
2. **Enable 2-Step Verification** (if not already enabled)
3. **Generate App Password**:
   - Go to: https://myaccount.google.com/apppasswords
   - Select app: **Mail**
   - Select device: **Windows Computer**
   - Click **Generate**
   - Copy the 16-character password (example: `abcd efgh ijkl mnop`)

### Step 2: Update `.env` File

Open your `.env` file and update these lines (around line 49-51):

```env
MAIL_USERNAME=raphaelnibishaka@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_FROM_ADDRESS="raphaelnibishaka@gmail.com"
```

**Replace:**
- `raphaelnibishaka@gmail.com` with your actual Gmail address
- `abcdefghijklmnop` with your 16-character app password (remove spaces)

### Step 3: Restart Server

After updating `.env`:

```bash
# Stop the server (Ctrl+C)
php artisan config:clear
php artisan serve
```

## 📬 What the Welcome Email Contains

When a user registers, they receive an email with:

- ✅ Welcome message with their name
- ✅ Their email address
- ✅ **JWT Access Token** (full token)
- ✅ Instructions on how to use the token
- ✅ Link to login

### Example Email Content:

```
Hello John Doe!

Welcome to our application! Your account has been successfully created.

Here are your login credentials:
Email: john@example.com

Your Access Token:
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...

You can use this token to access the API by including it in the Authorization header:
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJ...

[Login to Dashboard Button]

Thank you for joining us!
Best regards, Laravel Team
```

## 🧪 Testing

### Test Registration (After configuring email):

**Postman Request:**
```
POST http://127.0.0.1:8000/api/auth/register
Content-Type: application/json
Accept: application/json

Body:
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Expected Response:**
```json
{
  "message": "User registered successfully. A welcome email with your access token has been sent to your email.",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "created_at": "2026-02-05T15:00:00.000000Z",
    "updated_at": "2026-02-05T15:00:00.000000Z"
  }
}
```

**Check Email:**
- The user will receive a welcome email with their JWT token
- They can copy the token and use it immediately

## 🔍 Troubleshooting

### Email Not Sending?

1. **Check `.env` credentials are correct**
2. **Clear config cache**: `php artisan config:clear`
3. **Restart server**: Stop (Ctrl+C) and run `php artisan serve` again
4. **Check Gmail settings**: Make sure 2FA is enabled and app password is generated
5. **Check logs**: `storage/logs/laravel.log` for error details

### Gmail Blocking?

- Make sure you're using an **App Password**, not your regular Gmail password
- Verify 2-Step Verification is enabled on your Google account
- Check if "Less secure app access" needs to be enabled (though app passwords should work)

### Still Not Working?

Run this command to test email configuration:
```bash
php artisan tinker
```

Then in tinker:
```php
Mail::raw('Test email', function($message) {
    $message->to('your-email@gmail.com')->subject('Test');
});
```

If this fails, check the error message for specific issues.

## 📋 Current `.env` Configuration

Your current email settings should be:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com          # ⚠️ UPDATE THIS
MAIL_PASSWORD=your-app-password             # ⚠️ UPDATE THIS
MAIL_FROM_ADDRESS="your-gmail@gmail.com"    # ⚠️ UPDATE THIS
MAIL_FROM_NAME="${APP_NAME}"
```

## ✨ Features

- ✅ **Automatic Token Generation**: JWT token created on registration
- ✅ **Email Delivery**: Welcome email sent automatically
- ✅ **Token in Email**: Users receive their access token via email
- ✅ **Secure**: Uses Gmail's secure SMTP with app passwords
- ✅ **Professional**: Nicely formatted email with instructions

---

**Once you add your email credentials, registration will automatically send welcome emails with tokens!** 🎉
