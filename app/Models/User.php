<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

	public function isTeacher()
	{
		return $this->getTeacher() ? true : false;
	}

	public function getTeacher()
	{
		return Teacher::where('user_id', $this->id)->first();
	}

	public function isStudent()
	{
		return $this->getStudent() ? true : false;
	}

	public function getStudent()
	{
		return Student::where('user_id', $this->id)->first();
	}

	public function courses()
	{
		$this->belongsTo(Student::class);
	}
}
