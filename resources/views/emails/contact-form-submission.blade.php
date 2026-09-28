<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0056b3;
            margin-top: 0;
        }
        .field {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .field:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            color: #333;
            word-wrap: break-word;
        }
        .message-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #0056b3;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.8em;
            text-align: center;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Contact Form Submission</h1>
        <p>You have received a new message through the contact form on your website.</p>
        
        <div class="field">
            <span class="label">Name:</span>
            <span class="value">{{ $name }}</span>
        </div>
        
        <div class="field">
            <span class="label">Email:</span>
            <span class="value"><a href="mailto:{{ $email }}">{{ $email }}</a></span>
        </div>
        
        @if($company)
        <div class="field">
            <span class="label">Company:</span>
            <span class="value">{{ $company }}</span>
        </div>
        @endif
        
        @if($reason)
        <div class="field">
            <span class="label">Reason for Contact:</span>
            <span class="value">{{ ucfirst(str_replace('-', ' ', $reason)) }}</span>
        </div>
        @endif
        
        @if($budget)
        <div class="field">
            <span class="label">Budget Range:</span>
            <span class="value">
                @php
                    $budgetLabels = [
                        'under-1k' => 'Under $1,000',
                        '1k-5k' => '$1,000 - $5,000',
                        '5k-10k' => '$5,000 - $10,000',
                        '10k-25k' => '$10,000 - $25,000',
                        '25k-plus' => '$25,000+',
                        'not-sure' => 'Not Sure Yet'
                    ];
                    echo $budgetLabels[$budget] ?? ucfirst(str_replace('-', ' ', $budget));
                @endphp
            </span>
        </div>
        @endif
        
        @if($timeline)
        <div class="field">
            <span class="label">Timeline:</span>
            <span class="value">
                @php
                    $timelineLabels = [
                        'asap' => 'ASAP',
                        '1-2-weeks' => '1-2 Weeks',
                        '1-month' => 'Within 1 Month',
                        '2-3-months' => '2-3 Months',
                        'flexible' => 'Flexible'
                    ];
                    echo $timelineLabels[$timeline] ?? ucfirst(str_replace('-', ' ', $timeline));
                @endphp
            </span>
        </div>
        @endif
        
        @if($subject)
        <div class="field">
            <span class="label">Subject:</span>
            <span class="value">{{ $subject }}</span>
        </div>
        @endif
        
        <div class="field">
            <span class="label">Message:</span>
            <div class="message-box">{{ $formMessage }}</div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated notification email. Please reply directly to {{ $email }} to respond to the sender.</p>
        </div>
    </div>
</body>
</html>
