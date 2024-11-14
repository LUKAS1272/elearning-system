<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\User;
use App\Models\CourseStudent;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
//		return CourseStudent::where('student_id', Auth::id())->where('course_id', 2)->first();

//		return User::find(1)->getStudent();

        $courses = [];

		if (Auth::id()) {
			$user = User::find(Auth::id());
			$courses = Course::all();
		} else {
			return 'a';
		}

        return view('courses.index', compact(['courses', 'user']));
    }

	public function signUp(Request $request)
	{
		return $request;
	}

    public function create(Request $request)
    {
        $course = new Course;
        $course->title = $request->input('title');
        $course->description = $request->input('description');
        $course->teacher_id = User::find(Auth::id())->getTeacher()['id'];
        $course->save();

        return redirect('/courses');
    }

	public function enroll(Request $request, $id)
	{
		Auth::user()->getStudent()->enroll($id);
		return redirect('/courses');
	}

    public function show($id)
    {
//		return Assignment::find(3)->grade->student->user;

        $course = Course::findOrFail($id);
		$studentsInCourse = CourseStudent::where('course_id', $id)->get();

        return view('courses.show', compact(['course', 'id', 'studentsInCourse']));
    }
}
