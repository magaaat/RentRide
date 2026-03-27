@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<h3 class="mb-3">Record Payment for Booking #{{ $booking->id }}</h3>
<form method="POST" action="{{ route('payments.store', $booking) }}">
    @csrf
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Payment Method</label>
            <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="payment_status" class="form-select">
                @foreach(['pending','paid','failed','refunded'] as $status)
                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Payment Date</label>
        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->format('Y-m-d')) }}">
    </div>
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection

