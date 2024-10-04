<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// php artisan route:list --path=api

Route::apiResource("/mentors", MentorController::class);
Route::apiResource("/courses", CourseController::class);
Route::apiResource("/chapters", ChapterController::class);
