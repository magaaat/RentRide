@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Students</h1>
        <p class="mt-1 text-sm text-slate-400">Tenant-specific student records for demo and CRUD verification.</p>
    </div>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
        {{ session('status') }}
    </div>
@endif

<div class="mb-6 rounded-xl border rr-border rr-surface p-4 shadow-rr">
    <h2 class="mb-3 text-sm font-semibold text-slate-200">Add Student</h2>
    <form method="POST" action="{{ route('students.store') }}" class="grid gap-3 md:grid-cols-3">
        @csrf
        <input type="text" name="student_id" placeholder="Student ID" value="{{ old('student_id') }}" class="rounded-lg border rr-border rr-input px-3 py-2 text-sm text-slate-100" required>
        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" class="rounded-lg border rr-border rr-input px-3 py-2 text-sm text-slate-100" required>
        <input type="text" name="address" placeholder="Address" value="{{ old('address') }}" class="rounded-lg border rr-border rr-input px-3 py-2 text-sm text-slate-100">
        <div class="md:col-span-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
                Save Student
            </button>
        </div>
    </form>
    @if ($errors->any())
        <div class="mt-3 text-sm text-rose-300">
            {{ $errors->first() }}
        </div>
    @endif
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface shadow-rr">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Student ID</th>
                <th class="px-4 py-3 text-left font-semibold">Name</th>
                <th class="px-4 py-3 text-left font-semibold">Address</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--rr-border);">
            @forelse($students as $student)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">{{ $student->student_id }}</td>
                    <td class="px-4 py-3">{{ $student->name }}</td>
                    <td class="px-4 py-3">{{ $student->address ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('students.update', $student) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="student_id" value="{{ $student->student_id }}" class="w-28 rounded-md border rr-border rr-input px-2 py-1 text-xs text-slate-100" required>
                                <input type="text" name="name" value="{{ $student->name }}" class="w-36 rounded-md border rr-border rr-input px-2 py-1 text-xs text-slate-100" required>
                                <input type="text" name="address" value="{{ $student->address }}" class="w-36 rounded-md border rr-border rr-input px-2 py-1 text-xs text-slate-100">
                                <button type="submit" class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-600">
                                    Update
                                </button>
                            </form>
                            <form
                                method="POST"
                                action="{{ route('students.destroy', $student) }}"
                                data-confirm
                                data-confirm-title="Delete student?"
                                data-confirm-text="This student record will be removed."
                                data-confirm-button="Yes, delete student"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-lg bg-red-600/90 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">No students found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $students->links() }}
</div>
@endsection
