@php
    use App\Models\Grade;
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $course->title }}</h1>
    <p>{{ $course->description }}</p>
    <p><strong>Teacher:</strong> {{ $course->teacher->user->name }}</p>
    <p><a href="{{ route('courses.index') }}">Back to all courses</a></p>
    <hr>
    <h3>Assignments</h3>
    @if(count($course->assignments) == 0)
        <p>There is no assignment at the moment</p>
    @endif

    @foreach($course->assignments as $assignment)
        @if(Auth::user()->isTeacher() || (Auth::user()->isStudent() && $assignment->student->user['id'] == Auth::user()['id']))
            <div class="card mb-3">
                <div class="card-body">
                    <h5>{{ $assignment->name }}</h5>
                    <div>{{ $assignment->content }}</div>
                    <hr>
                    <p><strong>Student:</strong> {{ $assignment->student->user['name'] }}</p>

                    @if(Auth::user()->isStudent())
                        @if(!$assignment->grade)
                            <form action="{{ route('turnin.assignment', [$id, $assignment['id']]) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="title">Solution:</label>
                                    <textarea name="solution" id="solution" cols="30" rows="10" class="form-control">{{ $assignment['solution'] }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-secondary">Turn in</button>
                            </form>
                        @else
                            <p>Graded solution: <strong>{{ $assignment['solution'] }}</strong></p>
                        @endif
                    @elseif(Auth::user()->isTeacher())
                        <p>Solution: <strong>{{ $assignment['solution'] ?? "missing!" }}</strong></p>
                    @endif


                    @if(!Grade::where('assignment_id', $assignment['id'])->exists())
                        @if(Auth::user()->isTeacher())
                            <form action="{{ route('grade.assignment', [$id, $assignment['id']]) }}" method="POST" class="d-inline-flex">
                                @csrf
                                <input type="text" name="grade_value" class="form-control" required>
                                <button type="submit" class="btn btn-secondary" style="margin-left: 0.5rem;">Grade</button>
                            </form>
                            <form action="{{ route('grade-auto.assignment', [$id, $assignment['id']]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Grade automatically</button>
                            </form>
                        @endif
                    @else
                        @php
                            $grade = Grade::where('assignment_id', $assignment['id'])->first();
                        @endphp

                        <p>Graded <strong>{{ $grade['grade'] }}</strong> by <strong>{{ $grade->teacher->user['name'] }}</strong></p>
                    @endif

                    <p>Due date: <strong>{{ $assignment['due_date'] }}</strong></p>
                </div>
            </div>
        @endif
    @endforeach

    @if(Auth::user()->isTeacher())
        <hr>
        <h2>Create a New Assignment</h2>
        <form action="{{ route('create.assignment', $id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Assignment Name:</label>
                <input type="text" name="name" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="description">Student:</label>
                <select class="form-select" aria-label="Default select example" required name="student_id">
                    <option selected value="">Select student</option>
                    @foreach($studentsInCourse as $student)
                        <option value="{{ $student['student_id'] }}">{{ $student->student->user['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="datePicker" class="font-weight-bold">Due date:</label>
                <input type="date" id="datePicker" class="form-control" name="due" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Assignment</button>
        </form>
    @endif
</div>
@endsection