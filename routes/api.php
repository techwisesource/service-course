<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// php artisan route:list --path=api

Route::apiResource("/mentors", MentorController::class);
Route::apiResource("/courses", CourseController::class);
Route::apiResource("/chapters", ChapterController::class);
Route::apiResource("/lessons", LessonController::class);
