<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query();

        $q = $request->query('q');
        $status = $request->query('status');

        $courses->when($q, function($query) use ($q) {
            return $query->whereRaw("name LIKE '%".strtolower($q)."%'");
        });

        $courses->when($status, function($query) use ($status) {
            return $query->where("status", "=", $status);
        });

        $result = $courses->paginate(2);

        if($result->isEmpty()) {
            return response()->json([
                "status" => "error",
                "message" => "data not found"
            ]);
        }

        return response()->json([
            "status" => "success",
            "data" => $result
        ]);
    }

    public function show($id)
    {
        $course = Course::with("chapters.lesson")
                        ->with("mentor")
                        ->with("images")
                        ->find($id);
        if(!$course) {
            return response()->json([
                "status" => "success",
                "message" => "Course Not Found"
            ]);
        }

        $reviews = Review::where("course_id", "=", $id)->get()->toArray();
        if(count($reviews) > 0) {
            $userIds = array_column($reviews, "user_id");
            $users = getUserById($userIds);
            // echo "<pre>".print_r($users, 1)."</pre>";
            if($users["status"] === "error") {
                $reviews = [];
            } else {
                foreach($reviews as $key => $review) {
                    $userIndex = array_search($review["user_id"], array_column($users['data'], 'id'));
                    $reviews[$key]["users"] = $users["data"][$userIndex];
                }
            }
        }

        $totalStudent = MyCourse::where("course_id", "=", $id)->count();
        $totalVideos = Chapter::where("course_id", "=", $id)->withCount("lesson")->get()->toArray();
        $finalTotalVideos = array_sum(array_column($totalVideos, "lessons_count"));

        $course["reviews"] = $reviews;
        $course["total_videos"] = $finalTotalVideos;
        $course["totalStudent"] = $totalStudent;

        return response()->json([
            "status" => "success",
            "data" => $course
        ]);
    }

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

    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "string",
            "certificate" => "boolean",
            "thumbnail" => "string|url",
            "type" => "string|in:free,premium",
            "status" => "string|in:draft,published",
            "price" => "integer",
            "level" => "string|in:all-level,beginner,intermediate,advance",
            "mentor_id" => "integer",
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

        $course = Course::find($id);
        // echo"<pre>".print_r($id)."</pre>";

        if(!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ], 404);
        }

        $mentorId = $request->input("mentor_id");
        if($mentorId) {
            $mentor = Mentor::find($mentorId);
            if(!$mentor) {
                return response()->json([
                    "status" => "error",
                    "message" => "mentor not found"
                ]);
            }
        }

        $course->fill($data);
        $course->save();

        return response()->json([
            "status" => "success",
            "data" => $course
        ]);
    }

    public function destroy($id)
    {
        $course = Course::find($id);
        if(!$course) {
            return response()->json([
                "status" => "error",
                "message" => "course not found"
            ]);
        }

        $course->delete($id);
        return response()->json([
            "status" => "success",
            "message" => "course deleted"
        ]);
    }
}
