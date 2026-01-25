<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .quote-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .quote-details table {
            width: 100%;
        }
        .quote-details td {
            padding: 8px 0;
        }
        .quote-details td:first-child {
            font-weight: 600;
            color: #6b7280;
            width: 40%;
        }
        .cta-button {
            display: inline-block;
            background: #4F46E5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .message {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">New Quote</p>
    </div>

    <div class="content">
        <h2 style="margin-top: 0;">Hello {{ $quote->client->name }},</h2>
        
        <p>Thank you for your interest! We're pleased to send you the following quote:</p>

        @if($message)
            <div class="message">
                {{ $message }}
            </div>
        @endif

        <div class="quote-details">
            <table>
                <tr>
                    <td>Quote Number:</td>
                    <td><strong>{{ $quote->quote_number }}</strong></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ $quote->created_at->format('F d, Y') }}</td>
                </tr>
                @if($quote->title)
                    <tr>
                        <td>Project:</td>
                        <td>{{ $quote->title }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Total Amount:</td>
                    <td><strong style="font-size: 18px; color: #4F46E5;">{{ $quote->currency }}{{ number_format($quote->total, 2) }}</strong></td>
                </tr>
                @if($quote->valid_until)
                    <tr>
                        <td>Valid Until:</td>
                        <td>{{ $quote->valid_until->format('F d, Y') }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <p>The quote is attached to this email as a PDF. Please review it carefully and let us know if you have any questions.</p>

        <center>
            <a href="mailto:{{ $company['email'] }}" class="cta-button">Reply to This Quote</a>
        </center>

        <p style="margin-top: 30px;">We look forward to working with you!</p>

        <p>Best regards,<br>
        <strong>{{ $quote->user->name }}</strong><br>
        {{ $company['name'] }}</p>
    </div>

    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong></p>
        @if($company['email'])
            <p>{{ $company['email'] }}</p>
        @endif
        @if($company['phone'])
            <p>{{ $company['phone'] }}</p>
        @endif
        <p style="margin-top: 15px; font-size: 12px;">
            This is an automated message. Please do not reply directly to this email.
        </p>
    </div>
</body>
</html>
