<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function automaticGrading($course_id, $assignment_id)
    {
		if (!Grade::where('assignment_id', $assignment_id)->exists()) {
			$assignment = Assignment::find($assignment_id);
			$grade = new Grade;
			$grade->assignment_id = $assignment_id;
			$grade->student_id = $assignment->student_id;
			$grade->teacher_id = Auth::user()->getTeacher()['id'];
			$grade->grade = rand(50, 100); // Simulated grading
			$grade->save();
		}

        return back();
    }

	public function grade(Request $request, $course_id, $assignment_id)
	{
//		return $assignment_id;
//		return Grade::where('assignment_id', $assignment_id)->exists();
		if (!Grade::where('assignment_id', $assignment_id)->exists()) {
			$assignment = Assignment::find($assignment_id);
			$grade = new Grade;
			$grade->assignment_id = $assignment_id;
			$grade->student_id = $assignment->student_id;
			$grade->teacher_id = Auth::user()->getTeacher()['id'];
			$grade->grade = $request->input('grade_value');
			$grade->save();

//			return $grade;
		}

		return back();
	}
}
