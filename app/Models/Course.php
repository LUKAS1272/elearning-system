<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

	public function students()
	{
		return $this->belongsToMany(Student::class);
	}

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}
