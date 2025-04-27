<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: #f7fafc;
            color: #4a5568;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 40px;
        }
        h1 {
            color: #2d3748;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .emoji {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .highlight {
            color: #3182ce;
            font-weight: 700;
        }
        .balance {
            background: #ebf8ff;
            color: #2b6cb0;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 700;
            display: inline-block;
            margin: 10px 0;
            border: 1px solid #90cdf4;
        }
        .contact {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #718096;
        }
        .btn {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #3182ce;
        }
        .digital-countdown {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px 0;
            font-family: 'Courier New', monospace;
        }
        .time-unit {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 5px;
        }
        .time-value {
            background-color: #ebf8ff;
            color: #2b6cb0;
            font-size: 2rem;
            font-weight: 700;
            padding: 10px 15px;
            border-radius: 5px;
            min-width: 60px;
            text-align: center;
            box-shadow: 0 0 10px rgba(49, 130, 206, 0.3);
            font-family: 'Digital-7', 'Courier New', monospace;
            border: 1px solid #90cdf4;
        }
        .time-label {
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 5px;
            color: #2d3748;
        }
        .time-separator {
            font-size: 2rem;
            font-weight: 700;
            margin-top: -20px;
            color: #000;
        }
        .developer {
            background: #ebf8ff;
            color: #2b6cb0;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            margin: 15px 0;
            border-left: 4px solid #4299e1;
        }
        .warning {
            background: #ebf8ff;
            color: #2b6cb0;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 700;
            margin: 20px 0 10px;
            border: 1px solid #90cdf4;
        }
        .warning-icon {
            font-size: 1.2rem;
            margin-right: 5px;
        }
        .ilonggo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3182ce;
            margin: 15px 0;
            font-style: italic;
        }
        .ilonggo-small {
            font-style: italic;
            color: #4a5568;
            font-size: 1rem;
        }
        .quote {
            margin-top: 20px;
            font-style: italic;
            color: #2d3748;
            font-size: 1rem;
            font-weight: 600;
        }
        .bayad-po {
            font-size: 3.5rem;
            font-weight: 900;
            color: #3182ce;
            margin: 10px 0;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            transform: rotate(-5deg);
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">


        <h1 class="bayad-po">BALANCE</h1>

        <div class="balance">
           ₱2,500.00
        </div>


        <p class="ilonggo-small">
            The server ran out of energy... and budget.
        </p>
        <img src="https://media.giphy.com/media/8YutMatqkTfSE/giphy.gif" alt="Crying Meme" style="width: 200px; margin-top: 15px; border-radius: 10px;">


        <div class="digital-countdown">
            <div class="time-unit">
                <div class="time-value" id="days">00</div>
                <div class="time-label">DAYS</div>
            </div>
            <div class="time-separator">:</div>
            <div class="time-unit">
                <div class="time-value" id="hours">00</div>
                <div class="time-label">HOURS</div>
            </div>
            <div class="time-separator">:</div>
            <div class="time-unit">
                <div class="time-value" id="minutes">00</div>
                <div class="time-label">MINS</div>
            </div>
            <div class="time-separator">:</div>
            <div class="time-unit">
                <div class="time-value" id="seconds">00</div>
                <div class="time-label">SECS</div>
            </div>
        </div>

        <p class="destruction-notice">When countdown reaches ZERO, <span class="highlight">please contact us to restore service</span></p>

        <p class="quote">
            "True appreciation comes when you are the one who made the effort."
        </p>



        {{-- <p class="contact">
            If you believe this is an error or you've already made a payment, please contact our support team at <span class="highlight">support@agri-marketplace.com</span>
        </p>

        <a href="mailto:support@agri-marketplace.com" class="btn">Contact Support</a> --}}
    </div>

    <script>
        // Set the date we're counting down to (10 days from now)
        const countDownDate = new Date();
        countDownDate.setDate(countDownDate.getDate() + 10);

        // Update the countdown every 1 second
        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerHTML = days < 10 ? '0' + days : days;
            document.getElementById("hours").innerHTML = hours < 10 ? '0' + hours : hours;
            document.getElementById("minutes").innerHTML = minutes < 10 ? '0' + minutes : minutes;
            document.getElementById("seconds").innerHTML = seconds < 10 ? '0' + seconds : seconds;

            if (distance < 0) {
                clearInterval(x);
                document.getElementById("countdown").innerHTML = "EXPIRED";
            }
        }, 1000);
    </script>
</body>
</html>
