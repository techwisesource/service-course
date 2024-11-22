<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ImageCourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// php artisan route:list --path=api

Route::apiResource("/mentors", MentorController::class);
Route::apiResource("/courses", CourseController::class);
Route::apiResource("/chapters", ChapterController::class);
Route::apiResource("/lessons", LessonController::class);
Route::apiResource("/image-course", ImageCourseController::class);
Route::apiResource("/reviews", ReviewController::class);

// Route::apiResource("/my-courses", MyCourseController::class);
Route::post("/my-courses", [MyCourseController::class, "store"]);
Route::post("/my-courses/premium", [MyCourseController::class, "createPremiumAccess"]);
