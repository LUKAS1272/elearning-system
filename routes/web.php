<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckAuth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(CheckAuth::class)->group(function () {
	Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
	Route::post('/courses/create', [CourseController::class, 'create'])->name('create.course');
	Route::post('/courses/enroll/{id}', [CourseController::class, 'enroll'])->name('enroll');
	Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

	Route::post('/courses/{id}/create-assignment', [AssignmentController::class, 'create'])->name('create.assignment');
	Route::post('/courses/{idC}/turnin/{idA}', [AssignmentController::class, 'turnIn'])->name('turnin.assignment');
	Route::post('/courses/{idC}/grade/{idA}', [GradeController::class, 'grade'])->name('grade.assignment');
	Route::post('/courses/{idC}/grade-auto/{idA}', [GradeController::class, 'automaticGrading'])->name('grade-auto.assignment');
});

Route::get('/grade/automatic/{assignment_id}', [GradeController::class, 'automaticGrading']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');