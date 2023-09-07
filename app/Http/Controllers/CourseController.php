<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ApiTrait\FunctionTemplateTrait;
use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Resources\AddVideoNotifyResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use App\Models\Video;
use App\Notifications\AddVideo;
use App\Notifications\CreateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    use ResponseTrait;
    use FunctionTemplateTrait;
    /**
     *  Get all courses available
     * @return mixed
     */
    public function index(){
        return Course::get() ? $this->successResponse(Course::get()) : $this->errorResponse();
    }

    /**
     *  Find course's members using id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function findCourse($id){
        return Course::find($id) ? $this->successResponse(UserResource::collection(Course::find($id)->users)) : $this->errorResponse();
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
            $path = $this->upload($request , 'photo', 'image' , 'image-course-'.Course::get()->count()+1);
            $course = Course::create(array_merge(
                $validator->validated(),
                [
                    'image' => $path,
                    'description' =>$request->description,
                ]
            ));

        // send Notifications for all admin
//        $admins = $this->admins();
//        $course_id = $course['id'];
//        $course_title = $course['title'];
//        $course_creator = auth()->id;
//        Notification::send($admins, new C($course_id, $course_title, $course_creator));

            return $this->apiResponse($course,201,'added successfully');
        }
        return $this->errorValidateResponse('There is a course with the same Title and level');
    }

    /**
     *
     *  Edit Course Info
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'level'=>'required|between:0,100',
            'hours'=>'required|string',
            'teacher_id'=>'required',
            'price'=>'required',
            'photo' => 'required|mimes:jpg,png'
        ]);
        if($validator->fails()){
            // status = 400
            return $this->errorValidateResponse($validator->errors());
        }
        $exists = Course::where('title', $request->title)->where('level', $request->level)->get('id');
        if(!$exists->count()){
            $path = $this->upload($request , 'photo', 'image' , null);
            $course = Course::find($request->course_id)->Update(array_merge(
                $validator->validated(),
                [
                    'image' => $path,
                    'description' =>$request->description,
                ]
            ));
            return $this->apiResponse(Course::find($request->course_id),201,'Updated successfully');
        }else{
            if($exists[0]->id == $request->course_id){
                $path = $this->upload($request , 'photo', 'image' , null);
                $course = Course::find($request->course_id)->Update(array_merge(
                    $validator->validated(),
                    [
                        'image' => $path,
                        'description' =>$request->description,
                    ]
                ));
                return $this->apiResponse($course,201,'Updated successfully');
            }
            return $this->errorValidateResponse('There is a course with the same Title and level');
        }
    }


    /**
     *
     *  Add Video to Course using Course ID
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVideo( Request $request){

        $course_id = $request->course_id;
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'description'=>'string',
            'video'=>'required|mimes:mp4,webm,ogg,ogx',
        ]);
        if($validator->fails()){
            // status = 400
            return $this->errorValidateResponse($validator->errors());
        }
//        //--------------------------------------
//        $file = $request->file('video');
//        $file_name = $file->getClientOriginalName();
//        $file->move('course/'.$course_id, $file_name);
//
//        $insert = new Video();
//        $insert->video = $file_name;
//        $insert->title = $request->title;
//        $insert->description = $request->description;
//        $insert->course_id = $course_id;
//        $insert->save();
//        return redirect()->back();
//        //------------------------------
//
        $exists = Video::where('video', 'course/'. $course_id.'/' .$request->file('video')->getClientOriginalName())->get();

        if(! $exists->count()){
            $path = $this->upload($request,'video','video','course/'.$course_id);
            $video = Video::create([
                'title' => $request->title,
                'description' => $request->description,
                'video' => $path,
                'course_id' => $course_id,
            ]);

        // send Notifications for all User are joined to course
            $users = Course::find($course_id)->users;
            $title = $request->title;
            $description = $request->description;
            $teacher_id = auth()->user()->id;
            Notification::send($users, new AddVideo($course_id, $title, $description, $teacher_id));

            Event::create([
                'event' => auth()->user()->name ." (with id = ". auth()->user()->id . '), has added a new video to his course : '. Course::where('id' , $course_id)->first()->title,
                'user_id' => auth()->user()->id,
            ]);
            return $this->apiResponse($video,201,'added successfully');
        }
        return $this->errorValidateResponse('There is a video with the same source name');

    }

    /**
     *  Get All notifications about courses
     */
    public function notifyMe(){
        $user_id = User::find(auth()->user()->id);
        $unread_notifications_count =
            $user_id->unreadnotifications->where('type' ,'App\Notifications\AddVideo')->count();
        $unread_notifications =
            $user_id->unreadnotifications->where('type' ,'App\Notifications\AddVideo');
        $readed_notifications =
            $user_id->notifications->where('type' ,'App\Notifications\AddVideo')
                ->where('read_at','!=' , null);
        $data = [
            'unread_notifications_count' => $unread_notifications_count,
            'unread_notifications' => AddVideoNotifyResource::collection($unread_notifications),
            'readed_notifications' => AddVideoNotifyResource::collection($readed_notifications),
        ];
        return $this->successResponse($data);
    }

    /**
     *  Get Course INFO
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData($id){
        return Course::find($id)? $this->successResponse(Course::find($id)) : $this->errorResponse();
    }


    /**
     *  Videos
     */
    public function showVideos($id){
        if(!Video::where('course_id' , $id)->get('video')->count()){
            $videos = Video::where('course_id' , $id)->get('video');
            $i = 0 ;
            $url = [];
            foreach ($videos as $video){
                $url [$i] = 'http://127.0.0.1:8000/videos/'.$video['video'] ;
                $i++ ;
            }
            return $this->successResponse($url);
        }
        else{
            return $this->errorValidateResponse("the ");
        }
        //        return '<video src='. $url[0].' controls loop autoplay></video>';
    }
}
