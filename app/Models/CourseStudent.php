<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function courses()
	{
		return $this->hasMany(Course::class);
	}
}
