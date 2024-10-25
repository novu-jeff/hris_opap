<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations on Your New Position!</title>
</head>
<body>
    
    <h1>Congratulations on Your New Position!</h1>
    
    <p>Dear <strong>{{ $data['fullname'] }}</strong>,</p>

    <p>We are pleased to inform you that you have been hired for the position of <strong>{{ $data['position'] }}</strong> at <strong>{{ $data['company_name'] }}</strong>, located in <strong>{{ $data['location'] }}</strong>.</p>

    <h2>Your Employment Setup:</h2>
    <ul>
        <li><strong>Type:</strong> {{ $data['type'] }}</li>
        <li><strong>Salary Range:</strong> {{ $data['range'] }}</li>
    </ul>

    <p>Please note that you will receive another email detailing your start date.</p>

    <h2>Your Employee Account Credentials:</h2>
    <ul>
        <li><strong>Email:</strong> {{ $data['email'] }}</li>
        <li><strong>Password:</strong> {{ $data['password'] }}</li>
    </ul>

    <p>We encourage you to change your password immediately after logging in.</p>

    <p>Congratulations once again!</p>

    <p>Best regards,<br>
    HR Department<br>
</body>
</html>
