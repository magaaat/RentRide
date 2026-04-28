<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student instanceof Student ? $student->id : $student;

        return [
            'student_id' => ['required', 'string', 'max:64', Rule::unique('students', 'student_id')->ignore($studentId)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }
}
