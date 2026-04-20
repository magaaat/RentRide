<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff account created</title>
</head>
<body>
    <h2>Hello {{ $staffUser->name }},</h2>

    <p>
        An administrator has created a <strong>staff</strong> account for you on
        <strong>{{ $tenant->company_name }}</strong> (RentRide).
    </p>

    <p><strong>Sign in using your company domain:</strong></p>
    <p style="font-size: 15px;">
        <a href="{{ $loginUrl }}" target="_blank" rel="noopener noreferrer">{{ $loginUrl }}</a>
    </p>

    @if(!empty($loginDomain))
        <p style="font-size: 13px; color: #555;">
            Company login domain: <strong>{{ $loginDomain }}</strong>
        </p>
    @endif

    <p>Use this email address to log in: <strong>{{ $staffUser->email }}</strong></p>

    <p>Your temporary password is: <strong>{{ $plainPassword }}</strong></p>
    <p style="font-size: 13px; color: #555;">Please sign in and change your password after your first login.</p>

    <p>Thank you,<br>{{ $tenant->company_name }}</p>
</body>
</html>
