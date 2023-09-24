<?php

namespace App\Http\Controllers\API\Courses;

use App\Http\Controllers\API\ApiTrait\FunctionTemplateTrait;
use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddVideoNotifyResource;
use App\Http\Resources\CourseCommentResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use App\Models\Video;
use App\Notifications\AddVideo;
use App\Notifications\CreateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index()
    {
        try {
            return $this->successResponse(CourseResource::collection(Course::get()));
        } catch (\Exception $exception) {
            return $this->errorResponse();
        }

//        $courses = CourseResource::collection(Course::get());
//        $data = [];
//        foreach ($courses as $course) {
//            $teacher = User::find($course->teacher_id);
//            $data[] = [
//                'course' => $course,
//                'teacher' => new UserResource($teacher),
//            ];
//        }
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
                'course_info' => new CourseResource($course_) ,
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
            return User::find($id)->tracherCoursesName->count()? $this->successResponse(CourseResource::collection(User::find($id)->tracherCoursesName)) : $this->errorResponse();
        }
        return $this->errorResponse();
    }

    /**
     *  Get all courses that joined by a user
     * @param $id
     * @return mixed
     */
    public function userCourses($id){
        $tempId = 0;
        if(auth()->user()->admin){
            $tempId = $id;
        }else {
            $tempId = auth()->user()->id;
        }

        if($user = User::find($tempId)){
            if($user->courses->count()){
                return CourseResource::collection(User::find($tempId)->courses);
//                return $this->successResponse(User::find($id)->courses);
            }
            return 'Not joined to any course yet.';
//            return $this->processResponse('The user has not joined to any course yet');
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
        try{
            if(auth()->user()->id == Course::find($request->course_id)->teacher_id) {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string|between:2,100',
                    'level'=>'required|between:0,100',
                    'hours'=>'required|string',
                    'price'=>'required',
                    'photo' => 'required|mimes:jpg,png'
                ]);
                if($validator->fails()){
                    return $this->errorValidateResponse($validator->errors());
                }
//                Storage::delete(Course::find($request->course_id)->image);
                $path = $this->upload($request , 'photo', 'image' , 'image-course-'.$request->course_id);
                $course = Course::find($request->course_id)->update(array_merge(
                    $validator->validated(),
                    [
//                        'teacher_id' => auth()->user()->id,
                        'image' => $path,
                        'description' =>$request->description,
                    ]
                ));
                return $this->apiResponse(Course::find($request->course_id),201,'Updated successfully');
            }else{
                return $this->errorValidateResponse('You Can not edit the following Course.');
            }
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
//        $validator = Validator::make($request->all(), [
//            'title' => 'required|string|between:2,100',
//            'level'=>'required|between:0,100',
//            'hours'=>'required|string',
////            'teacher_id'=>'required',
//            'price'=>'required',
//            'photo' => 'required|mimes:jpg,png'
//        ]);
//
//        if($validator->fails()){
//            // status = 400
//            return $this->errorValidateResponse($validator->errors());
//        }
//        $exists = Course::where('title', $request->title)->where('level', $request->level)->get('id');
//        if(!$exists->count()){
//            $path = $this->upload($request , 'photo', 'image' , null);
//            $course = Course::find($request->course_id)->Update(array_merge(
//                $validator->validated(),
//                [
//                    'teacher_id' => auth()->user()->id,
//                    'image' => $path,
//                    'description' =>$request->description,
//                ]
//            ));
//            return $this->apiResponse(Course::find($request->course_id),201,'Updated successfully');
//        }else{
//            if($exists[0]->id == $request->course_id){
//                $path = $this->upload($request , 'photo', 'image' , null);
//                $course = Course::find($request->course_id)->Update(array_merge(
//                    $validator->validated(),
//                    [
//                        'teacher_id' => auth()->user()->id,
//                        'image' => $path,
//                        'description' =>$request->description,
//                    ]
//                ));
//                return $this->apiResponse($course,201,'Updated successfully');
//            }
//            return $this->errorValidateResponse('There is a course with the same Title and level');
//        }
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
            Notification::send($users, new AddVideo($course_id, $video->id, $title, $description, $teacher_id));

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
    public function notifyMe()
    {
        try {
            $user_id = Auth::id();
            $unread_notifications_count = User::find($user_id)
                ->unreadnotifications
                ->where('type', 'App\Notifications\AddVideo')
                ->count();

            $unread_notifications = User::find($user_id)
                ->unreadnotifications
                ->where('type', 'App\Notifications\AddVideo');

            $readed_notifications = User::find($user_id)
                ->notifications
                ->where('type', 'App\Notifications\AddVideo')
                ->whereNotNull('read_at');

            $data = [
                'unread_notifications_count' => $unread_notifications_count,
                'unread_notifications' => AddVideoNotifyResource::collection($unread_notifications),
                'readed_notifications' => AddVideoNotifyResource::collection($readed_notifications),
            ];

            if(auth()->user()->admin){
                $data['new_registration'] = User::find($user_id)
                    ->unreadnotifications
                    ->where('type', 'App\Notifications\CreateUser');

                $data['old_registration'] = User::find($user_id)
                    ->notifications
                    ->where('type', 'App\Notifications\CreateUser')
                    ->whereNotNull('read_at');
            }

            return $this->successResponse($data);
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     *  Get Course INFO
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData($id){
        try {
            if(! $course = Course::find($id)){
                return $this->errorResponse();
            }

            $commentsController = new CourseCommentController();
            $commentsResponse = $commentsController->index($id);

            foreach ($commentsResponse as $comment_){
                $comments[]  = $comment_;
            }
            $teacher = new  UserResource(User::find($course->teacher_id));
            foreach ($this->showVideos($id) as $video){
                $videos[] = $video;
            }
//            $videos = $this->showVideos($id);

            $data = [
                'course' => new CourseResource($course),
                'teacher' => $teacher,
                'comments'  => $comments[1]['data'],
                'video' => $videos[1]['data']
            ];

            return $this->successResponse($data);
        }catch (\Exception $e){
            return $this->errorResponse();
        }
    }


    public function subscribe(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $courseId = $request->course_id;

            $course = Course::find($courseId);

            if (!$course) {
                return $this->errorResponse("Course not found.");
            }

            if ($course->teacher_id != $userId) {
                // Check if the subscription already exists
                $existingSubscription = DB::table('user_course_pivot')
                    ->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->first();

                if ($existingSubscription) {
                    return $this->successResponse("Already subscribed to this course.");
                }

                // Create a new subscription
                $sub = DB::table('user_course_pivot')->insert([
                    'user_id' => $userId,
                    'course_id' => $courseId,
                ]);

                if ($sub) {
                    return $this->successResponse("Joined successfully.");
                } else {
                    return $this->errorResponse();
                }
            } else {
                return $this->errorValidateResponse('You created this course.');
            }
        } catch (\Exception $exception) {
            return $this->errorResponse();
        }
    }


    /**
     *  Videos
     */
    public function showVideos($id)
    {
        try {
            $course = Course::find($id);
            if ($course) {
                $videos = Video::where('course_id', $id)->get();
                if ($videos->count() != 0) {
                    $data = [];
                    foreach ($videos as $video) {
                        $data[] = [
                            'title' => $video->title,
                            'description' => $video->description,
                            'url' => 'http://127.0.0.1:8000/videos/' . $video->video
                        ];
                    }
                    return $this->successResponse($data);
                }
                return $this->errorValidateResponse("the course has not any video yet");
            }
            return $this->errorResponse();

        } catch (\Exception $exception) {
            return $this->errorResponse();
        }

//        if(!Video::where('course_id' , $id)->get('video')->count()){
//            $videos = Video::where('course_id' , $id)->get('video');
//            $i = 0 ;
//            $url = [];
//            foreach ($videos as $video){
//                $url [$i] = 'http://127.0.0.1:8000/videos/'.$video['video'] ;
//                $i++ ;
//            }
//            return $this->successResponse($url);
//        }
//        else{
//            return $this->errorValidateResponse("the course has not any video yet");
//        }
//        //        return '<video src='. $url[0].' controls loop autoplay></video>';
    }

    public function showVideo($id)
    {
        try {
            return DB::table('notifications')->where('id' ,$id)
                ->update(['read_at' => now(),])?$this->successResponse('READED'):$this->errorResponse();

//            if ($video = Video::find($id)) {
//                $url = 'http://127.0.0.1:8000/videos/'.$video['video'] ;
//                $userId = auth()->user()->id;
//                $courseId = $video->course_id;
//                $videoId = $id;
//
//                $notification = DB::table('notifications')->where('notifiable_id', $userId)
//                    ->where('type', 'App\Notifications\AddVideo')
//                    ->where('data->course_id', $courseId)
//                    ->where('data->video_id', $videoId)
//                    ->first();
//
//                if ($notification) {
//                    DB::table('notifications')->where('id', $notification->id)
//                        ->update([
//                            'read_at' => now(),
//                        ]);
//                }
//                return $this->successResponse($url);
//            } else {
//                return $this->errorValidateResponse("ERROR in Fetching Video");
//            }
        } catch (\Exception $e) {
            return $this->errorResponse();
        }
    }

//    public function showVideo($id){
//        try {
//            if($video = Video::find($id)){
//                $notifications = User::find(auth()->user()->id)
//                    ->unreadnotifications
//                    ->where('type', 'App\Notifications\AddVideo')
//                    ->where('data->"course_id"', $video->course_id);
//
//                return $notifications;
//
////                Notification::where('id', $notifications->id)->update([
////                    'read_at' => now(),
////                ]);
////                return $this->successResponse($url);
//            }
//            else{
//                return $this->errorValidateResponse("ERROR in Fetching Video");
//            }
//        }catch (\Exception $e){
//            return $this->errorResponse();
//        }
//    }



    /**
     *  Search for courses using request
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try{
            $search = Course::where('title', 'LIKE', '%' . $request->search . '%')
                ->orWhere('description', 'LIKE', '%' . $request->search . '%')
                ->orWhere('created_at', 'LIKE', '%' . $request->search . '%')
                ->get();

            $id = $search->pluck('teacher_id')->toArray();

            $suggestion = Course::whereNotIn('id', $search->pluck('id'))
                ->whereIn('teacher_id', $id)
                ->get();

            $result = [
                'result' => CourseResource::collection($search),
                'suggestion' => CourseResource::collection($suggestion)
            ];

            return $this->successResponse($result);

        }catch (\Exception $e){
            return $this->apiResponse($e, 500 ,'Bad Request');
        }
    }
}
