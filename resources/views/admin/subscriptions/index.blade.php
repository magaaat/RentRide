@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Subscriptions</h1>
    <p class="mt-1 text-sm text-slate-400">Subscription history</p>
</div>

<div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-800/80 text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Plan</th>
                <th class="px-4 py-3 text-left font-semibold">Price</th>
                <th class="px-4 py-3 text-left font-semibold">Period</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
            @forelse($subscriptions as $sub)
                <tr>
                    <td class="px-4 py-3">{{ $sub->plan_name }}</td>
                    <td class="px-4 py-3">₱{{ number_format((float) $sub->price, 2) }}</td>
                    <td class="px-4 py-3 text-slate-400">{{ $sub->start_date?->format('Y-m-d') }} to {{ $sub->end_date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $sub->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">No subscription rows yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
