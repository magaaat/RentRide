@extends('layouts.app')

@section('title', 'Edit customer')

@section('content')
<h3 class="mb-3 text-xl font-semibold">Edit customer</h3>
<form method="POST" action="{{ route('customers.update', $customer) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address) }}">
        </div>
    </div>
    <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">Update</button>
    <a href="{{ route('customers.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-4 py-2 text-sm font-semibold">Cancel</a>
</form>
@endsection
