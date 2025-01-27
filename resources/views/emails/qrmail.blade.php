<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code for Distribution</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0fdf4; /* Light green background */
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
            border: 2px solid #22c55e; /* Green border */
        }
        .email-header {
            /* background-color: #16a34a;  */
            /* color: white; */
            text-align: center;
            padding: 20px;
        }
        .email-header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
        }
        .email-body p {
            line-height: 1.6;
            margin: 10px 0;
        }
        .email-body strong {
            color: #16a34a; /* Dark green for highlights */
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            width: 200px;
            height: 200px;
        }
        .email-footer {
            text-align: center;
            font-size: 14px;
            color: #666666;
            padding: 10px;
            border-top: 1px solid #eeeeee;
            background-color: #f0fdf4; /* Match the body background */
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
            <p>Dear <strong>{{ $beneficiary->name }}</strong>,</p>
            <p>
                You are a beneficiary of the <strong>{{ $distribution->title }}</strong> distribution.
            </p>
            <p>
                <strong>Item:</strong> {{ $beneficiary->distributionItem->item->name }}<br>
                <strong>Distribution Date:</strong> {{ $distribution->distribution_date }}<br>
                <strong>Location:</strong> {{ $distribution->location }}
            </p>
            <p>
                Below is your QR code to claim your item:
            </p>
            <div class="qr-code">
                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($beneficiary->code, 'QRCODE') }}" alt="QR Code">
            </div>
            <p>Please present this QR code at the distribution site to claim your item.</p>
            <p>Thank you,<br><strong>Agri Distribution Project</strong></p>
        </div>
        <!-- Footer Section -->
        <div class="email-footer">
            If you have any questions, please contact us at <strong>support@agriproject.com</strong>.
        </div>
    </div>
</body>
</html>
