<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code for Distribution</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd; /* Light blue background */
            margin: 0;
            padding: 0;
        }
        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 40px auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #1e88e5; /* Blue border */
        }
        .email-header {
            background-color: #1e88e5;
            color: white;
            text-align: left;
            padding: 20px;
            display: flex;
            align-items: center;
        }
        .email-header img {
            width: 60px;
            margin-right: 15px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
        }
        .email-body p {
            line-height: 1.6;
            margin: 10px 0;
        }
        .highlight {
            color: #1e88e5; /* Blue for highlights */
            font-weight: bold;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            width: 180px;
            height: 180px;
        }
        .email-footer {
            text-align: center;
            font-size: 14px;
            color: #666666;
            padding: 15px;
            border-top: 1px solid #eeeeee;
            background-color: #e3f2fd; /* Match the body background */
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <img src="{{url('images/bg.png')}}" alt="Farm Icon">
            <h1>QR Code for Distribution</h1>
        </div>
        <!-- Body Section -->
        <div class="email-body">
            <p>Dear <span class="highlight">{{ $beneficiary->name }}</span>,</p>
            <p>
                You are a beneficiary of the <span class="highlight">{{ $distribution->title }}</span> distribution.
            </p>
            <p>
                <span class="highlight">Item:</span> {{ $beneficiary->distributionItem->item->name }}<br>
                <span class="highlight">Distribution Date:</span> {{ \Carbon\Carbon::parse($distribution->distribution_date)->format('F j, Y') }}<br>
                <span class="highlight">Location:</span> {{ $distribution->location }}
            </p>
            <p>
                Below is your QR code to claim your item:
            </p>
            <div class="qr-code">
                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($beneficiary->code, 'QRCODE') }}" alt="QR Code">
            </div>
            <p>Please present this QR code at the distribution site to claim your item.</p>
            <p>Thank you,<br><span class="highlight">Agri Distribution Project</span></p>
        </div>
        <!-- Footer Section -->
        <div class="email-footer">
            If you have any questions, please contact us at <span class="highlight">support@agriproject.com</span>.
        </div>
    </div>
</body>
</html>
