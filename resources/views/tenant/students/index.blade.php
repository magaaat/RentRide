@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Students</h1>
            <p class="text-sm text-slate-500">Tenant test module for update verification.</p>
        </div>
    </div>

    <div class="rr-panel-elevated mb-6 p-5">
        <h2 class="mb-3 text-sm font-semibold">Add student</h2>
        <form method="POST" action="{{ route('tenant.students.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
            @csrf
            <input type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Student ID" class="rr-input" required>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" class="rr-input" required>
            <input type="text" name="address" value="{{ old('address') }}" placeholder="Address" class="rr-input">
            <div class="md:col-span-3">
                <button class="rr-btn-primary inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold">Save student</button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Student ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Address</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($students as $student)
                        <tr>
                            <td class="px-4 py-3">{{ $student->student_id }}</td>
                            <td class="px-4 py-3">{{ $student->name }}</td>
                            <td class="px-4 py-3">{{ $student->address ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('tenant.students.destroy', $student) }}" class="inline-block" data-confirm data-confirm-title="Delete student?" data-confirm-text="This action cannot be undone." data-confirm-button="Yes, delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No students yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>
@endsection
