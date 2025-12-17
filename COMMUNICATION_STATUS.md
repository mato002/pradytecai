# Communication Configuration Status

## Overview
This document shows the current status of all communication channels and their API configurations.

## ✅ Configured Communication Channels

### 1. **Email (Laravel Mail)**
- **Status**: ✅ Configured
- **Service**: Laravel Mail (SMTP)
- **Configuration File**: `config/mail.php`
- **Usage**: 
  - Job application replies
  - Enquiry/contact message replies
- **Required Environment Variables**:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailtrap.io  # or your SMTP provider
  MAIL_PORT=2525
  MAIL_USERNAME=your-username
  MAIL_PASSWORD=your-password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@pradytec.com
  MAIL_FROM_NAME="${APP_NAME}"
  ```
- **Status**: ✅ Working (requires SMTP credentials in `.env`)

### 2. **SMS (BulkSMS CRM API)**
- **Status**: ✅ Configured
- **Service**: BulkSMS CRM API
- **API URL**: `https://crm.pradytecai.com/api`
- **Configuration File**: `config/services.php`
- **Usage**: Job application notifications
- **Required Environment Variables**:
  ```env
  BULKSMS_API_URL=https://crm.pradytecai.com/api
  BULKSMS_API_KEY=your-api-key-here
  BULKSMS_CLIENT_ID=1
  BULKSMS_SENDER_ID=YOUR-SENDER-ID
  ```
- **How to Get Credentials**:
  1. Log in to https://crm.pradytecai.com
  2. Go to Settings → Profile
  3. Copy your API Key
  4. Use your approved Sender ID (max 11 characters)
- **Status**: ⚠️ Requires API credentials in `.env`

### 3. **WhatsApp (Ultramsg API)**
- **Status**: ✅ Configured
- **Service**: Ultramsg API
- **API URL**: `https://api.ultramsg.com`
- **Configuration File**: `config/services.php`
- **Usage**: Job application notifications
- **Required Environment Variables**:
  ```env
  ULTRAMSG_API_URL=https://api.ultramsg.com
  ULTRAMSG_INSTANCE_ID=your-instance-id
  ULTRAMSG_TOKEN=your-token
  ```
- **How to Get Credentials**:
  1. Sign up at https://ultramsg.com
  2. Create an instance
  3. Get your Instance ID and Token from the dashboard
- **Status**: ⚠️ Requires API credentials in `.env`

## 📋 Implementation Details

### CommunicationService (`app/Services/CommunicationService.php`)
- ✅ `sendEmail(JobApplication, message, subject)` - For job applicants
- ✅ `sendEmailToRecipient(email, subject, message, name)` - For enquiries/contact messages
- ✅ `sendSMS(JobApplication, message)` - Via BulkSMS CRM API
- ✅ `sendWhatsApp(JobApplication, message)` - Via Ultramsg API
- ✅ `sendMultiple(JobApplication, channels[], message, subject)` - Multi-channel sending

### Controllers Using CommunicationService
1. **JobApplicationController** (`app/Http/Controllers/Admin/JobApplicationController.php`)
   - ✅ `sendMessage()` - Sends via selected channels (email, SMS, WhatsApp)
   - ✅ Uses `sendMultiple()` method

2. **EnquiryController** (`app/Http/Controllers/Admin/EnquiryController.php`)
   - ✅ `reply()` - Sends email replies to contact enquiries
   - ✅ Uses `sendEmailToRecipient()` method

## 🔧 Recent Fixes

### Fixed Issues:
1. ✅ **Enquiry Email Bug Fixed**: The `EnquiryController` was calling `sendEmail()` with wrong parameters. Fixed by:
   - Created `EnquiryReplyMail` mailable class
   - Added `sendEmailToRecipient()` method to `CommunicationService`
   - Updated `EnquiryController` to use the correct method

## ⚙️ Configuration Checklist

To fully enable all communications, ensure these are set in your `.env` file:

### Email Configuration
- [ ] `MAIL_MAILER` set to `smtp`
- [ ] `MAIL_HOST` configured (e.g., smtp.mailtrap.io, smtp.gmail.com)
- [ ] `MAIL_PORT` set (usually 587 for TLS, 465 for SSL)
- [ ] `MAIL_USERNAME` set
- [ ] `MAIL_PASSWORD` set
- [ ] `MAIL_ENCRYPTION` set (tls or ssl)
- [ ] `MAIL_FROM_ADDRESS` set
- [ ] `MAIL_FROM_NAME` set

### SMS Configuration (BulkSMS CRM)
- [ ] `BULKSMS_API_URL` set (default: https://crm.pradytecai.com/api)
- [ ] `BULKSMS_API_KEY` set (get from BulkSMS CRM dashboard)
- [ ] `BULKSMS_CLIENT_ID` set (usually 1)
- [ ] `BULKSMS_SENDER_ID` set (your approved sender ID)

### WhatsApp Configuration (Ultramsg)
- [ ] `ULTRAMSG_API_URL` set (default: https://api.ultramsg.com)
- [ ] `ULTRAMSG_INSTANCE_ID` set (get from Ultramsg dashboard)
- [ ] `ULTRAMSG_TOKEN` set (get from Ultramsg dashboard)

## 🧪 Testing

### Test Email
```bash
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

### Test SMS
- Ensure BulkSMS CRM account has balance
- Send message via admin panel: `/admin/applications/{id}`
- Check logs: `storage/logs/laravel.log`

### Test WhatsApp
- Ensure Ultramsg instance is active
- Send message via admin panel: `/admin/applications/{id}`
- Check logs: `storage/logs/laravel.log`

## 📚 Documentation
- Full setup guide: `COMMUNICATION_SETUP.md`
- API Documentation: https://crm.pradytecai.com/api-documentation

## ⚠️ Notes
- All API calls are logged to `storage/logs/laravel.log`
- Phone numbers are automatically formatted to international format (254 for Kenya)
- Email uses Laravel's built-in mail system (supports SMTP, Mailgun, SES, etc.)
- SMS and WhatsApp require active API accounts with sufficient balance/credits


