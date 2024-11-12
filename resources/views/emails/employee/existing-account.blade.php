<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Account</title>
</head>
<body>

    <p>Hello <strong>{{ ucwords($data['fullname']) }}</strong>,</p>

    <h4>Your Employee Account Credentials:</h4>
    <ul>
        <li><strong>Email:</strong> {{ $data['email'] }}</li>
        <li><strong>Password:</strong> {{ $data['password'] }}</li>
    </ul>

    <p>We encourage you to change your password immediately after logging in.</p>

    <p>Congratulations once again!</p>

    <p>Best regards,<br>
    HR Department</p>

</body>
</html>
