<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
	public function courses()
	{
		return $this->belongsToMany(Course::class);
	}

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function isEnrolled($course_id)
	{
		return CourseStudent::where('student_id', $this->id)->where('course_id', $course_id)->exists();
	}

	public function enroll($course_id)
	{
		if (!CourseStudent::where('course_id', $course_id)->where('student_id', $this->id)->exists()) {
			CourseStudent::insert(['student_id' => $this->id, 'course_id' => $course_id]);
		}
	}
}
