<?php

namespace App\Http\Controllers;

use App\Models\ImageCourse;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function index(Request $request)
    {
        $imageCourse = ImageCourse::query();
        $courseId = $request->input("course_id");

        $imageCourse->when($courseId, function($query) use ($courseId){
            return $query->where("course_id", "=", $courseId);
        });

        return response()->json([
            "status" => "success",
            "data" => $imageCourse
        ]);
    }

    public function show($id)
    {
        $imageCourse = ImageCourse::find($id);
        if(!$imageCourse) {
            return response()->json([
                "status" => "error",
                "message" => "Image course not found"
            ]);
        }

        return response()->json([
            "status" => "success",
            "data" => $imageCourse
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            "image" => "required|url",
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

        $imageCourse = ImageCourse::create($data);
        return response()->json([
            "status" => "success",
            "data" => $imageCourse
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "image" => "url",
            "course_id" => "integer"
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if(!$validator) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ]);
        }

        $courseId = $request->input("course_id");
        if($courseId) {
            $course = Course::find($courseId);
            if(!$course) {
                return response()->json([
                    "status" => "error",
                    "message" => "course not found"
                ]);
            }
        }

        $imageCourse = ImageCourse::find($id);
        if(!$imageCourse) {
            return response()->json([
                "status" => "error",
                "message" => "image course not found"
            ]);
        }

        $imageCourse->fill($data);
        $imageCourse->save();
        return response()->json([
            "status" => "success",
            "data" => $imageCourse
        ]);
    }
    public function destroy($id)
    {
        $imageCourse = ImageCourse::find($id);
        if(!$imageCourse) {
            return response()->json([
                "status" => "error",
                "message" => "imageCourse not found"
            ]);
        }

        $imageCourse->delete($id);
        return response()->json([
            "status" => "success",
            "data" => "image course deleted"
        ]);
    }
}
