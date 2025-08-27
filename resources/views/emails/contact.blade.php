<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CollabConnect Support Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            color: #374151;
            font-size: 14px;
        }
        .message-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            white-space: pre-wrap;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎯 CollabConnect Support Request</h1>
        <p>New support request from {{ $user->name }}</p>
    </div>

    <div class="content">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Category</div>
                <div class="info-value">{{ $categoryLabel }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Account Type</div>
                <div class="info-value">{{ $user->account_type->label() }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">User Email</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">User ID</div>
                <div class="info-value">#{{ $user->id }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Response Time Promise</div>
                <div class="info-value">{{ $responseTime }} business {{ $responseTime == 1 ? 'day' : 'days' }}</div>
            </div>
        </div>

        <h3>Subject: {{ $subject }}</h3>

        <div class="message-content">{{ $messageContent }}</div>
    </div>

    <div class="footer">
        <p>This message was sent via the CollabConnect support form.</p>
        <p>You can reply directly to this email to respond to {{ $user->name }}.</p>
        <p><strong>⏰ Response Time Promise:</strong> {{ $responseTime }} business {{ $responseTime == 1 ? 'day' : 'days' }}</p>
    </div>
</body>
</html>