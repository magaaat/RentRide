<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::query()
            ->latest()
            ->paginate(12);

        return view('tenant.students.index', compact('students'));
    }

    public function store(StoreStudentRequest $request)
    {
        Student::create($request->validated());

        return redirect()
            ->route('tenant.students.index')
            ->with('success', 'Student created.');
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return redirect()
            ->route('tenant.students.index')
            ->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()
            ->route('tenant.students.index')
            ->with('success', 'Student deleted.');
    }
}
