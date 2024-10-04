<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "course_id" => "required|integer"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $courseId = $request->input("course_id");
        $course = Course::find($courseId);
        if(!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $chapter = Chapter::create($data);
        return response()->json([
            "status" => "success",
            "data" => $chapter
        ]);
    }
}
