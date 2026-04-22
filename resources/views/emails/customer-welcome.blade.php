<h2>Welcome to RentRide</h2>

<p>Hello {{ $customerUser->name }},</p>

<p>Your customer account has been created by <strong>{{ $tenant->company_name }}</strong>.</p>

<p><strong>Login email:</strong> {{ $customerUser->email }}</p>
<p><strong>Temporary password:</strong> {{ $plainPassword }}</p>

<p>
    Login here:
    <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
</p>

<p>For security, please change your password after your first login.</p>
