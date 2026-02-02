<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        .logo-container {
            margin-bottom: 40px;
        }
        .logo {
            max-width: 300px;
            width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 42px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 700;
        }
        p {
            font-size: 20px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .message {
            font-size: 18px;
            color: #888;
            font-style: italic;
        }
        @media (max-width: 600px) {
            h1 {
                font-size: 32px;
            }
            .logo {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('img/client-logo.png') }}" alt="OPAP Logo" class="logo" onerror="this.style.display='none'">
        </div>
        <div class="icon">🔧</div>
        <h1>We're Under Maintenance</h1>
        <p>We'll be back soon</p>
        <p class="message">We're currently performing some updates to improve your experience. Please check back shortly.</p>
    </div>
</body>
</html>
