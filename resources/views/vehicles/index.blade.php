@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">Vehicles</h3>
    <a href="{{ route('vehicles.create') }}" class="inline-flex items-center rounded-lg rr-btn-primary px-3 py-1.5 text-xs font-semibold">
        Add Vehicle
    </a>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Image</th>
                <th class="px-4 py-3 text-left font-semibold">Name</th>
                <th class="px-4 py-3 text-left font-semibold">Type</th>
                <th class="px-4 py-3 text-left font-semibold">Brand</th>
                <th class="px-4 py-3 text-left font-semibold">Plate</th>
                <th class="px-4 py-3 text-left font-semibold">Price/Day</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--rr-border);">
            @forelse($vehicles as $vehicle)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">
                        @if($vehicle->image)
                            <button type="button"
                                    class="inline-flex items-center gap-2 text-xs text-slate-200 hover:underline"
                                    data-vehicle-image="{{ asset('storage/' . $vehicle->image) }}"
                                    data-vehicle-name="{{ $vehicle->vehicle_name }}">
                                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="Vehicle image" class="h-10 w-14 rounded-md object-cover border rr-border">
                                <span>View</span>
                            </button>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $vehicle->vehicle_name }}</td>
                    <td class="px-4 py-3">{{ $vehicle->vehicle_type }}</td>
                    <td class="px-4 py-3">{{ $vehicle->brand }}</td>
                    <td class="px-4 py-3">{{ $vehicle->plate_number }}</td>
                    <td class="px-4 py-3">₱{{ number_format($vehicle->price_per_day, 2) }}</td>
                    <td class="px-4 py-3 capitalize">{{ $vehicle->status }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
                            Edit
                        </a>
                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-400">No vehicles found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $vehicles->links() }}
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-vehicle-image]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const url = btn.getAttribute('data-vehicle-image');
                    const name = btn.getAttribute('data-vehicle-name') || 'Vehicle';
                    if (window.Swal) {
                        Swal.fire({
                            title: name,
                            imageUrl: url,
                            imageAlt: name,
                            showConfirmButton: true,
                            confirmButtonText: 'Close',
                        });
                    } else {
                        window.open(url, '_blank');
                    }
                });
            });
        });
    </script>
@endpush
@endsection

