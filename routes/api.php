<?php

use App\Http\Controllers\MentorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post("mentors", "MentorController@create");

Route::apiResource("/mentors", MentorController::class);
