<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Approved</title>
</head>
<body>
    <h2>Hello {{ $tenant->owner_name }},</h2>

    <p>Your rental company <strong>{{ $tenant->company_name }}</strong> has been approved on <strong>RentRide</strong>.</p>

    <p>You can now access your own dashboard using this link:</p>
    <p>
        <a href="{{ $loginUrl }}" target="_blank">
            {{ $loginUrl }}
        </a>
    </p>

    <p style="font-size: 13px; color: #555;">
        (Your assigned tenant domain is: <strong>{{ $loginDomain }}</strong>. For local testing we use the link above.)
    </p>

    <p>Use the email address <strong>{{ $tenant->email }}</strong> to log in.</p>

    @if(!empty($temporaryPassword))
        <p>Your temporary password is: <strong>{{ $temporaryPassword }}</strong></p>
        <p style="font-size: 13px; color: #555;">Please sign in and change your password after first login.</p>
    @else
        <p>Use the password you created during registration.</p>
    @endif

    <p>Thank you,<br>RentRide</p>
</body>
</html>

