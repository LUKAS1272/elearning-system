To create a PHP Laravel e-learning system based on the provided UML diagrams, I'll outline the process step-by-step, including the creation of migrations, models, controllers, views, and routes. The system will cover students, teachers, courses, assignments, tests, grading, and a grading system. Let's build a relatively complex system that aligns with the provided UML diagrams.

### Step 1: Setting up Laravel Environment
1. **Install Laravel:**
   ```bash
   composer create-project laravel/laravel elearning-system
   ```

2. **Configure the Database:** Update `.env` file with database credentials.

### Step 2: Database Migrations
We'll create migrations for `courses`, `teachers`, `students`, `assignments`, `tests`, `grades`, and other related tables.

#### 1. Teacher Migration
```php
php artisan make:migration create_teachers_table
```
Migration:
```php
Schema::create('teachers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
```

#### 2. Student Migration
```php
php artisan make:migration create_students_table
```
Migration:
```php
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
```

#### 3. Course Migration
```php
php artisan make:migration create_courses_table
```
Migration:
```php
Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->foreignId('teacher_id')->constrained('teachers');
    $table->timestamps();
});
```

#### 4. Assignment and Test Migration
```php
php artisan make:migration create_assignments_table
php artisan make:migration create_tests_table
```
Assignments Migration:
```php
Schema::create('assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained('courses');
    $table->foreignId('student_id')->constrained('students');
    $table->text('content');
    $table->timestamps();
});
```

Test Migration:
```php
Schema::create('tests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained('courses');
    $table->foreignId('student_id')->constrained('students');
    $table->text('questions');
    $table->timestamps();
});
```

#### 5. Grade Migration
```php
php artisan make:migration create_grades_table
```
Migration:
```php
Schema::create('grades', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')->nullable()->constrained('assignments');
    $table->foreignId('test_id')->nullable()->constrained('tests');
    $table->foreignId('student_id')->constrained('students');
    $table->foreignId('teacher_id')->constrained('teachers');
    $table->integer('grade');
    $table->timestamps();
});
```

### Step 3: Models
For each table, we create models that represent the data structures:
```bash
php artisan make:model Teacher
php artisan make:model Student
php artisan make:model Course
php artisan make:model Assignment
php artisan make:model Test
php artisan make:model Grade
```
Each model will have relationships that connect the entities:

**Teacher Model**:
```php
class Teacher extends Model
{
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
```

**Student Model**:
```php
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
}
```

**Course Model**:
```php
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
```

**Assignment Model**:
```php
class Assignment extends Model
{
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}
```

**Test Model**:
```php
class Test extends Model
{
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}
```

**Grade Model**:
```php
class Grade extends Model
{
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
```

### Step 4: Controllers and Routes
To handle the application's functionality, we'll create controllers for teachers, students, courses, assignments, and grades.

#### 1. Course Controller
```bash
php artisan make:controller CourseController
```

Controller snippet for managing courses:
```php
public function index()
{
    $courses = Course::all();
    return view('courses.index', compact('courses'));
}

public function create(Request $request)
{
    $course = new Course;
    $course->title = $request->input('title');
    $course->description = $request->input('description');
    $course->teacher_id = Auth::id();
    $course->save();

    if (Auth::check() && Auth::user()->hasRole('teacher')) {
        $teacher = Teacher::where('email', Auth::user()->email)->first();
        if (!$teacher) {
            $teacher = new Teacher;
            $teacher->name = Auth::user()->name;
            $teacher->email = Auth::user()->email;
            $teacher->password = Auth::user()->password;
            $teacher->save();
        }
    }

    return redirect()->route('courses.index');
}
```

Routes in `web.php`:
```php
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::post('/courses', [CourseController::class, 'create'])->name('courses.create');
```

#### 2. Grading System
We will create a `GradeController` to handle automatic and manual grading.

**GradeController**:
```bash
php artisan make:controller GradeController
```

Controller snippet for automatic grading:
```php
public function automaticGrading($assignment_id)
{
    $assignment = Assignment::find($assignment_id);
    $grade = new Grade;
    $grade->assignment_id = $assignment_id;
    $grade->student_id = $assignment->student_id;
    $grade->teacher_id = Auth::id();
    $grade->grade = rand(50, 100); // Simulated grading
    $grade->save();

    return back()->with('success', 'Graded automatically');
}
```

Route in `web.php` for automatic grading:
```php
Route::get('/grade/automatic/{assignment_id}', [GradeController::class, 'automaticGrading']);
```

### Step 5: Views
We create views using Laravel Blade templates for each entity and action.

#### Layout Template for Views
Create a new file named `app.blade.php` under `resources/views/layouts/`:

**layouts/app.blade.php**:
```blade
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
```

#### Example Blade File for Courses
Create a folder named `courses` under `resources/views` and add `index.blade.php`.

**courses/index.blade.php**:
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Courses</h1>
    @foreach($courses as $course)
        <div class="card">
            <div class="card-body">
                <h5>{{ $course->title }}</h5>
                <p>{{ $course->description }}</p>
                <a href="{{ route('courses.show', $course->id) }}">View Details</a>
            </div>
        </div>
    @endforeach
    <hr>
    <h2>Create a New Course</h2>
    <form method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Course Title:</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Course</button>
    </form>
</div>
@endsection
```

### Step 6: Authentication
Laravel provides built-in authentication using:
```bash
composer require laravel/ui
php artisan ui vue --auth
```
This will scaffold the authentication system needed to differentiate between students, teachers, and admins.

### Step 7: Additional Functionalities
- **Admin Features**: Create an `AdminController` to add reporting and management functionality.
- **Middleware for Roles**: Use middleware to ensure only specific roles (Admin, Teacher, Student) can access certain routes.

### Step 8: Testing
Ensure to test each part:
- Courses can be created and managed by teachers.
- Students can enroll and submit assignments.
- Grading can be automated or manually triggered by teachers.

The Laravel project based on these UML diagrams would be highly functional, allowing different roles to interact through distinct use cases such as course management, grading, and assignment submission.
