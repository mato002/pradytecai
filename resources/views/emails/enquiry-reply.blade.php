<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Pradytecai</h1>
    </div>
    
    <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;">
        @if($name)
            <p style="margin-top: 0;">Dear {{ $name }},</p>
        @else
            <p style="margin-top: 0;">Hello,</p>
        @endif
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            {!! nl2br(e($message)) !!}
        </div>
        
        <p style="margin-bottom: 0; color: #666; font-size: 14px;">
            Best regards,<br>
            <strong>The Pradytecai Team</strong>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 12px;">
        <p>This is an automated response to your enquiry. If you have further questions, please reply to this email.</p>
        <p>&copy; {{ date('Y') }} Pradytecai. All rights reserved.</p>
    </div>
</body>
</html>


