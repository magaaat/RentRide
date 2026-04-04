@extends('layouts.app')

@section('title', 'Booking calendar')

@section('content')
<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold">Booking calendar</h3>
        <p class="text-sm text-slate-400">{{ $start->format('F Y') }} — Standard / Premium</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('bookings.calendar', ['year' => $prev->year, 'month' => $prev->month]) }}" class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
            ← Prev
        </a>
        <a href="{{ route('bookings.calendar', ['year' => now()->year, 'month' => now()->month]) }}" class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
            Today
        </a>
        <a href="{{ route('bookings.calendar', ['year' => $next->year, 'month' => $next->month]) }}" class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
            Next →
        </a>
        <a href="{{ route('bookings.index') }}" class="rounded-lg rr-btn-primary px-3 py-1.5 text-xs font-semibold">
            List view
        </a>
    </div>
</div>

@php
    $weekdays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
@endphp

<div class="overflow-hidden rounded-xl border rr-border rr-surface">
    <div class="grid grid-cols-7 gap-px bg-slate-800/80 text-center text-xs font-semibold text-slate-400">
        @foreach($weekdays as $wd)
            <div class="bg-slate-950/80 py-2">{{ $wd }}</div>
        @endforeach
    </div>
    <div class="grid grid-cols-7 gap-px bg-slate-800/50">
        @for($pad = 0; $pad < $firstWeekday - 1; $pad++)
            <div class="min-h-[100px] bg-slate-950/40"></div>
        @endfor
        @for($d = 1; $d <= $daysInMonth; $d++)
            @php
                $dayDate = \Carbon\Carbon::create($year, $month, $d)->startOfDay();
                $dayBookings = $bookings->filter(function ($b) use ($dayDate) {
                    return $b->start_date->lte($dayDate) && $b->end_date->gte($dayDate);
                });
                $isToday = $dayDate->isToday();
            @endphp
            <div class="min-h-[100px] border-t border-slate-800/80 bg-slate-950/40 p-2 text-left {{ $isToday ? 'ring-1 ring-sky-500/50' : '' }}">
                <div class="text-xs font-semibold {{ $isToday ? 'text-sky-300' : 'text-slate-400' }}">{{ $d }}</div>
                <div class="mt-1 space-y-1">
                    @foreach($dayBookings->take(3) as $b)
                        <div class="truncate rounded bg-violet-500/15 px-1.5 py-0.5 text-[10px] text-violet-100" title="{{ $b->vehicle?->vehicle_name }} — {{ ucfirst($b->status) }}">
                            {{ $b->vehicle?->vehicle_name ?? 'Booking' }}
                        </div>
                    @endforeach
                    @if($dayBookings->count() > 3)
                        <div class="text-[10px] text-slate-500">+{{ $dayBookings->count() - 3 }} more</div>
                    @endif
                </div>
            </div>
        @endfor
    </div>
</div>
@endsection
