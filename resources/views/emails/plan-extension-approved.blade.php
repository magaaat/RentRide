<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plan Extension Approved</title>
</head>
<body>
    <h2>Hello {{ $tenant->owner_name }},</h2>

    <p>Your plan extension request has been <strong>approved</strong>.</p>

    <p>
        <strong>Company:</strong> {{ $tenant->company_name }}<br>
        <strong>Approved plan:</strong> {{ ucfirst($request->requested_plan) }}<br>
        <strong>New expiry date:</strong> {{ optional($tenant->subscription_expiry)->format('Y-m-d') }}
    </p>

    <p>You can log in again using this link:</p>
    <p>
        <a href="{{ $loginUrl }}" target="_blank">{{ $loginUrl }}</a>
    </p>

    <p>Thank you,<br>RentRide</p>
</body>
</html>

