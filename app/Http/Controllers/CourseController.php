<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "certificate" => "required|boolean",
            "thumbnail" => "string|url",
            "type" => "string|in:free,premium",
            "status" => "string|in:draft,published",
            "price" => "integer",
            "level" => "string|in:all-level,beginner,intermediate,advance",
            "mentor_id" => "required|integer",
            "description" => "string"
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $mentorId = $request->input("mentor_id");
        $mentor = Mentor::find($mentorId);
        if(!$mentor) {
            return response()->json([
                "status" => "error",
                "message" => "mentor not found"
            ], 404);
        }

        $course = Course::create($data);
        return response()->json([
            "status" => "success",
            "data" => $course
        ]);
    }
}
