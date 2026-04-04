<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Domain Updated</title>
</head>
<body>
    <h2>Hello {{ $tenant->owner_name }},</h2>

    <p>Your tenant login domain has been updated by the Super Admin.</p>

    <p>
        <strong>Company:</strong> {{ $tenant->company_name }}<br>
        @if(!empty($oldDomain))
            <strong>Previous domain:</strong> {{ $oldDomain }}<br>
        @endif
        <strong>New domain:</strong> {{ $newDomain }}
    </p>

    <p>Please use this updated login link going forward:</p>
    <p>
        <a href="{{ $loginUrl }}" target="_blank">{{ $loginUrl }}</a>
    </p>

    <p>Thank you,<br>RentRide</p>
</body>
</html>

