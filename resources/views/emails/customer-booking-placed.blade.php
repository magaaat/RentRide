<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking request</title>
</head>
<body>
    <h2>Hello {{ $booking->customer?->name ?? 'Customer' }},</h2>
    <p>We received your rental request on <strong>RentRide</strong>.</p>
    <ul>
        <li><strong>Company:</strong> {{ $booking->tenant->company_name }}</li>
        <li><strong>Vehicle:</strong> {{ $booking->vehicle->vehicle_name }} ({{ $booking->vehicle->vehicle_type }})</li>
        <li><strong>Dates:</strong> {{ $booking->start_date->format('Y-m-d') }} to {{ $booking->end_date->format('Y-m-d') }}</li>
        <li><strong>Status:</strong> {{ ucfirst($booking->status) }}</li>
    </ul>
    <p>The rental company will review and confirm your booking. You can track status in your customer dashboard.</p>
    <p>Thank you,<br>RentRide</p>
</body>
</html>
