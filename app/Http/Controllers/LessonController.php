<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "video" => "required|string",
            "chapter_id" => "required|integer"
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $chapterId = $request->input("chapter_id");
        $chapter = Chapter::find($chapterId);
        if(!$chapter) {
            return response()->json([
                "status" => "error",
                "message" => "chapter not found"
            ], 404);
        }

        $lesson = Lesson::create($data);
        return response()->json([
            "status" => "success",
            "data" => $lesson
        ]);
    }
}
