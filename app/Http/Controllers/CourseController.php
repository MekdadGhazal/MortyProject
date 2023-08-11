<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\User;
use App\Notifications\CreateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    use ResponseTrait;
    /**
     *  Get members of course
     * @return mixed
     */
    public function index($id){
        return Course::find($id) ? $this->successResponse(Course::find($id)->users) : $this->errorResponse();
    }

    /**
     *
     *  Get all Information about Course [name , member]
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fullCourseInfo($id){
        if(Course::find($id)){
            $course = Course::find($id);
            $course_ = Course::find($id);
            $mat = [
                'course_info' => $course_ ,
                'created_by' => new UserResource($course->teacher),
                'course_members' => UserResource::collection($course->users),
            ];
            return $this->successResponse($mat);
        }
        return $this->errorResponse();
    }

    /**
     *  Get all Courses that created by teacher
     * @param $id
     * @return mixed
     */
    public function courses($id){
        if(User::find($id)){
            return User::find($id)->tracherCoursesName->count()? $this->successResponse(User::find($id)->tracherCoursesName) : $this->errorResponse();
        }
        return $this->errorResponse();
    }

    /**
     *  Get all courses that joined by a user
     * @param $id
     * @return mixed
     */
    public function userCourses($id){
        if($user = User::find($id)){
            if($user->courses->count()){
                return $this->successResponse(User::find($id)->courses);
            }
            return $this->processResponse('The user has not joined to any course yet');
        }
        return $this->errorResponse();
    }

    public function insert(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'level'=>'required|between:0,100',
            'hours'=>'required|string',
            'teacher_id'=>'required',
            'price'=>'required',
            ]);

        if($validator->fails()){
            // status = 400
            return $this->errorValidateResponse($validator->errors());
        }
        $exists = Course::where('title', $request->title)->where('level', $request->level)->get();
        if(! $exists->count()){
            $course = Course::create(array_merge(
                $validator->validated(),
                [
                    'image' => $request->image,
                    'description' =>$request->description,
                ]
            ));

//        // send Notifications for all admin
//        $admins = $this->admins();
//        $course_id = $course['id'];
//        $course_title = $course['title'];
//        $course_creator = auth()->user()->id;
//        Notification::send($admins, new CreateUser($course_id, $course_title, $course_creator));

            return $this->apiResponse($course,201,'added successfully');
        }
        return $this->errorValidateResponse('There is a course with the same Title and level');
    }

    public function edit(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'level'=>'required|between:0,100',
            'hours'=>'required|string',
            'teacher_id'=>'required',
            'price'=>'required',
        ]);
        if($validator->fails()){
            // status = 400
            return $this->errorValidateResponse($validator->errors());
        }
        $exists = Course::where('title', $request->title)->where('level', $request->level)->get('id');
        if(!$exists->count()){
            $course = Course::find($id)->Update(array_merge(
                $validator->validated(),
                [
                    'image' => $request->image,
                    'description' =>$request->description,
                ]
            ));
            return $this->apiResponse($course,201,'Updated successfully');
        }else{
            if($exists[0]->id == $id){
                $course = Course::find($id)->Update(array_merge(
                    $validator->validated(),
                    [
                        'image' => $request->image,
                        'description' =>$request->description,
                    ]
                ));
                return $this->apiResponse($course,201,'Updated successfully');
            }
            return $this->errorValidateResponse('There is a course with the same Title and level');
        }
    }

}
