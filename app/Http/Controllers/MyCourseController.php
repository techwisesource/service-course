<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{

    public function index(Request $request)
    {
        $myCourses = MyCourse::query()->with("course");

        $userId = $request->query("user_id");

        /**
         * condition for debug user_id
         */
        // if(!$userId) {
        //     return response()->json([
        //         "status" => "error",
        //         "message" => "User ID is required"
        //     ], 400);
        // }

        $myCourses->when($userId, function($query) use ($userId) {
            return $query->where("user_id", "=", $userId);
        });

        return response()->json([
            "status" => "success",
            "data" => $myCourses->get()
        ]);
    }

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

        if($course->type === 'premium') {
            if($course->price === 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "Price can/'t be 0"
                ], 405);
            }

            $order = postOrder([
                "user" => $user['data'],
                "course" => $course->toArray()
            ]);

            if($order['status'] === 'error') {
                return response()->json([
                    'status' => $order['status'],
                    'message' => $order['message']
                ], $order['http_code']);
            }

            return response()->json([
                'status' => $order['status'],
                'data' => $order['data']
            ]);

        } else {
            $myCourse = MyCourse::create($data);
            return response()->json([
                "status" => "success",
                "data" => $myCourse
            ]);
        }

    }

    public function createPremiumAccess(Request $request)
    {
        $data = $request->all();
        $myCourse = MyCourse::create($data);

        // echo "<pre>".print_r($data)."</pre>";

        return response()->json([
            "status" => "success",
            "data" => $myCourse
        ]);
    }

}
