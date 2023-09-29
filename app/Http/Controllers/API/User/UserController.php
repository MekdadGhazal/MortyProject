<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\API\ApiTrait\FunctionTemplateTrait;
use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Controllers\API\Courses\CourseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use FunctionTemplateTrait;
    use ResponseTrait;

    public function login(Request $request){
        return $this->loginUser($request);
    }
    public function register(Request $request){
        return $this->RegisterUser($request);
    }
    public function logout(){
        return $this->logoutUser();
    }
//    public function admin()
//    {
//        return $this->admins();
//    }

    public function getData(){
        $courses = new CourseController();
        $list = $courses->userCourses(auth()->user()->id);

        $data = [
            'user' => new UserResource(auth()->user()),
            'joinedCourses' => $list,
            ];

        $userId = auth()->user()->id;
        if (auth()->user()->admin) {
            $create = Course::where('teacher_id', $userId)->count() ? CourseResource::collection(Course::where('teacher_id', $userId)->get()) : 'No Created course yet.';
            $posts = Post::where('user_id', $userId)->count() ?Post::where('user_id', $userId)->get() : 'No Created Posts yet.';
            $data['createdCourses'] = $create;
            $data['createdPost'] = $posts;
        }else{
            $data['createdCourses'] ='You are not an Admin to create courses.';
            $data['createdPost'] = 'You are not an Admin to create posts.';
        }
        return $this->successResponse($data);
    }

    public function insertData(Request $request){
        return $this->insertUserData($request);
    }
    public function destroy(): \Illuminate\Http\JsonResponse
    {
        DB::table('notifications')->where('data->id', auth()->user()->id)->delete();
        Event::create([
            'event' => 'The User with id:' .  auth()->user()->id . ', has deleted his account',
            'user_id' => auth()->user()->id,
        ]);
        User::destroy(auth()->user()->id);
        return $this->deleteResponse();
    }
    public function search(Request $request){
//        $search = User::where('name','LIKE', '%' . $request->search . '%')
//            ->orwhere ('email','LIKE', '%' . $request->search . '%')
//            ->orwhere ('created_at', 'LIKE','%' . $request->search . '%')
//            ->get();
//        $search = UserResource::collection($search);
//        return $this->apiResponse($search, 201, 'ok');
        return $this->searchByModel(User::class ,$request);
    }


    /**
     *  New Registeration
     * @return \Illuminate\Http\JsonResponse
     */
    public function Registeration()
    {
        try {
            return $this->successResponse(UserResource::collection(User::where('verify' ,0)->get()));
        }catch (\Exception $e){
            return $this->errorResponse();
        }
    }
}
