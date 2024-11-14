<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RequestMatcher\QueryParameterRequestMatcher;

class AssignmentController extends Controller
{
    public function create(Request $request, $id)
	{
//		return $request;

//		return Assignment::all()[0]->student->user['name'];

		$assignment = new Assignment();
		$assignment->content = $request->input('description');
		$assignment->name = $request->input('name');
		$assignment->student_id = intval($request->input('student_id'));
		$assignment->course_id = $id;
		$assignment->due_date = $request->input('due');
		$assignment->save();

		return redirect("/courses/$id");
	}

	public function turnIn(Request $request, $courseId, $assignmentId)
	{
		$assignment = Assignment::find($assignmentId);
		$assignment['solution'] = $request->input('solution');
		$assignment->save();

		return back();
	}
}
