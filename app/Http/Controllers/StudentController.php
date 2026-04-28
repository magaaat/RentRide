<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return redirect()->route('students.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255', 'unique:students,student_id'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('status', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        return redirect()->route('students.index');
    }

    public function edit(Student $student)
    {
        return redirect()->route('students.index');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255', 'unique:students,student_id,' . $student->id],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('status', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('status', 'Student deleted successfully.');
    }
}
