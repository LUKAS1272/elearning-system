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

                @if($user->isStudent() && !$user->getStudent()->isEnrolled($course->id))
                    <form action="{{ route('enroll', $course->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">Sign up to</button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
    @if(count($courses) == 0)
        No courses available at the moment
    @endif

    @if($user->isTeacher())
        <hr>
        <h2>Create a New Course</h2>
        <form action="{{ route('create.course') }}" method="POST">
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
    @endif
</div>
@endsection