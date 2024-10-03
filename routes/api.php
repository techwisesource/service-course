<?php

use App\Http\Controllers\MentorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// php artisan route:list --path=api

Route::apiResource("/mentors", MentorController::class);
