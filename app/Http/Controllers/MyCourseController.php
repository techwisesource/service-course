<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            "course_id" => "required|integer",
            "user_id" => "required|integer"
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ], 400);
        }

        $courseId = $request->input("course_id");
        $userId = $request->input("user_id");

        // Validasi course_id di database
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                "status" => "error",
                "message" => "Course not found"
            ], 404);
        }

        // Panggil service-user untuk mendapatkan data user
        $user = getUser($userId);
        if ($user["status"] === "error") {
            return response()->json([
                "status" => $user["status"],
                "message" => $user["message"]
            ], $user["http_code"]);
        }

        // Validasi apakah course sudah diambil oleh user
        $isExistsMyCourse = MyCourse::where("course_id", $courseId)
                                    ->where("user_id", $userId)
                                    ->exists();
        if ($isExistsMyCourse) {
            return response()->json([
                "status" => "error",
                "message" => "User already taken this course"
            ], 409);
        }

        // Jika validasi berhasil, lanjutkan proses penyimpanan
        $myCourse = MyCourse::create($data);
        return response()->json([
            "status" => "success",
            "data" => $myCourse
        ]);
    }

}
