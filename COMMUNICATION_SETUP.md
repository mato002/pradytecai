# Communication Module Setup Guide

This guide explains how to configure the communication module for sending messages to job applicants via Email, SMS, and WhatsApp.

## Environment Variables

Add these variables to your `.env` file:

### Email Configuration (Laravel Mailer)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pradytec.com
MAIL_FROM_NAME="${APP_NAME}"
```

For production, use your SMTP provider (Gmail, SendGrid, Mailgun, etc.)

### BulkSMS CRM API Configuration (SMS)

```env
BULKSMS_API_URL=https://crm.pradytecai.com/api
BULKSMS_API_KEY=your-api-key-here
BULKSMS_CLIENT_ID=1
BULKSMS_SENDER_ID=YOUR-SENDER-ID
```

**How to get your API credentials:**
1. Log in to your BulkSMS CRM dashboard at https://crm.pradytecai.com
2. Go to Settings → Profile
3. Copy your API Key
4. Note your Client ID (usually 1 for main account)
5. Use your approved Sender ID (max 11 characters)

**API Documentation:** https://crm.pradytecai.com/api-documentation

### Ultramsg Configuration (WhatsApp)

```env
ULTRAMSG_API_URL=https://api.ultramsg.com
ULTRAMSG_INSTANCE_ID=your-instance-id
ULTRAMSG_TOKEN=your-token
```

**How to get your Ultramsg credentials:**
1. Sign up at https://ultramsg.com
2. Create an instance
3. Get your Instance ID and Token from the dashboard

## Usage

1. Go to `/admin/applications/{id}` to view a job application
2. Scroll to the "Send Message" section in the sidebar
3. Select communication channels (Email, SMS, WhatsApp)
4. Enter your message
5. Click "Send Message"

## Phone Number Format

The system automatically formats phone numbers to international format:
- Input: `0712345678` or `+254712345678`
- Output: `254712345678` (for API calls)

## Testing

1. **Email**: Configure SMTP and send a test email
2. **SMS**: Ensure you have balance in your BulkSMS CRM account
3. **WhatsApp**: Verify your Ultramsg instance is active

## Troubleshooting

- Check `storage/logs/laravel.log` for detailed error messages
- Verify API credentials are correct in `.env`
- Ensure phone numbers are in correct format
- For SMS: Check your BulkSMS CRM account balance
- For WhatsApp: Verify Ultramsg instance is connected and active

