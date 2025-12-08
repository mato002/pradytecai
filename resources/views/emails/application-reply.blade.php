<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Pradytec</h1>
    </div>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <h2 style="color: #1f2937; margin-top: 0;">Hello {{ $application->name }},</h2>
        
        <p style="color: #4b5563; margin: 20px 0;">
            Thank you for your interest in the <strong>{{ $application->position->title }}</strong> position at Pradytec.
        </p>
        
        <div style="background: #f9fafb; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <p style="color: #1f2937; margin: 0; white-space: pre-wrap;">{{ $message }}</p>
        </div>
        
        <p style="color: #4b5563; margin: 20px 0;">
            If you have any questions, please don't hesitate to reach out to us.
        </p>
        
        <p style="color: #4b5563; margin: 20px 0 0 0;">
            Best regards,<br>
            <strong>The Pradytec Team</strong>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; padding: 20px; color: #6b7280; font-size: 12px;">
        <p style="margin: 0;">
            This is an automated message regarding your job application.<br>
            Pradytec - Enterprise Software Solutions
        </p>
    </div>
</body>
</html>

