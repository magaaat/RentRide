<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking status update</title>
</head>
<body>
    <h2>Hello {{ $booking->customer?->name ?? 'Customer' }},</h2>
    <p>Your booking status was updated on <strong>RentRide</strong>.</p>
    <ul>
        <li><strong>Company:</strong> {{ $booking->tenant->company_name }}</li>
        <li><strong>Vehicle:</strong> {{ $booking->vehicle->vehicle_name }}</li>
        <li><strong>Dates:</strong> {{ $booking->start_date->format('Y-m-d') }} to {{ $booking->end_date->format('Y-m-d') }}</li>
        <li><strong>Previous status:</strong> {{ ucfirst($previousStatus) }}</li>
        <li><strong>Current status:</strong> {{ ucfirst($booking->status) }}</li>
    </ul>
    <p>You can view details in your customer dashboard.</p>
    <p>Thank you,<br>RentRide</p>
</body>
</html>
