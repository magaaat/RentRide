@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold">Students</h1>
        <p class="text-sm text-slate-500">Manage tenant students for demo and internal records.</p>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <p class="font-semibold">Please fix the following:</p>
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <h2 class="mb-3 text-base font-semibold">Add Student</h2>
    <form method="POST" action="{{ route('students.store') }}" class="grid gap-3 md:grid-cols-3">
        @csrf
        <input type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Student ID (e.g. STU-0003)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <input type="text" name="address" value="{{ old('address') }}" placeholder="Address (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <div class="md:col-span-3">
            <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold">Create student</button>
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
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('students.update', $student) }}" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="student_id" value="{{ $student->student_id }}" class="w-28 rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                    <input type="text" name="name" value="{{ $student->name }}" class="w-40 rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                    <input type="text" name="address" value="{{ $student->address }}" class="w-40 rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                    <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Update</button>
                                </form>

                                <form method="POST" action="{{ route('students.destroy', $student) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
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
@endsection
