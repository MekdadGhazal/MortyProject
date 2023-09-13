<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiTrait\FunctionTemplateTrait;
use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Controllers\API\Courses\CourseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotify;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use ResponseTrait;
    use FunctionTemplateTrait;

    public function index(){
        try{
            return $this->successResponse(UserResource::collection(User::where('verify','!=',0)->get()));
        }catch (\Exception $e){
            return $this->errorResponse();
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return $this->errorResponse();
            }

            $courses = new CourseController();
            $lists = $courses->userCourses($id);

            $data = [
                'user' => new UserResource($user),
                'joinedCourses' => $lists
            ];

            if ($user->admin) {
                $create = Course::where('teacher_id', $id)->count() ? Course::where('teacher_id', $id)->get() : 'No Created course yet.';
                $data['createdCourses'] = $create;
            }

            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse();
        }
    }

    public function verifyUser($id){
        if (User::find($id)){
            if(User::find($id)->verify != 1){
                User::find($id)->update([
                    'verify' => 1,
                ]);
                $getID = DB::table('notifications')->where('data->id' , $id)->pluck('id');
                foreach ($getID as $notify){
                    DB::table('notifications')
                        ->where('id' , $notify)
                        ->update([
                            'read_at' => now(),
                            'data->by' => auth()->user()->name,
                        ]);
                }
                Event::create([
                    'event' => 'New User '. User::find($id)->name ." (with id = ".$id. '), has verified by '. auth()->user()->name,
                    'user_id' => auth()->user()->id,
                ]);
//                return $this->successResponse($getID);
                return $this->createResponse(new UserResource(User::find($id)), 'Verified Successfully');
            }
            return $this->processResponse();
        }
        return $this->errorResponse();
    }

    public function deleteUser($id){
        if (User::find($id)){
            Event::create([
                'event' => User::find($id)->name ." (with id = " .$id . '), has deleted by '. auth()->user()->name,
                'user_id' => auth()->user()->id,
            ]);
            DB::table('notifications')->where('data->id', $id)->delete();
            User::find($id)->destroy($id);
            return $this->deleteResponse();
        }
        return $this->errorResponse();
    }

    public function prompt($id){
        if (User::find($id)){
            //find user by id and determinate the role ::
            $role = User::find($id)->admin ? 'Admin': 'User';
            if($role == 'User') {
                $user = User::find($id);
                Admin::create([
                    'user_id' => $user->id,
                ]);
                Event::create([
                    'event' => User::find($id)->name ." (with id = " . $id . '), has prompted to Admin by '. auth()->user()->name,
                    'user_id' => auth()->user()->id,
                ]);
                return $this->createResponse(new UserResource($user),'A new Admin added success');
            }
            return $this->processResponse();
        }
        return $this->errorResponse();
    }

    public function owner($id)
    {
//        return count(Admin::where('owner',1)->get());
        if (User::find($id)){
            $role = User::find($id)->admin ? 'Admin': 'User';
            if($role == 'User'){
                $user = User::find($id);
                DB::table('admins')->update([
                    'owner' => 0,
                ]);
                Admin::create([
                    'user_id' => $user->id,
                    'owner' => 1,
                ]);
                Event::create([
                    'event' => User::find($id)->name ." (with id = ".$id. '), has prompted to Owner by '. auth()->user()->name,
                    'user_id' => auth()->user()->id,
                ]);
                return $this->createResponse(new UserResource(User::find($id)), 'A new Owner and Admin added success');
            }elseif ($role == 'Admin'){
                if(!User::find($id)->admin->owner){
                    DB::table('admins')->update([
                        'owner' => 0,
                    ]);
                    Admin::where('id',User::find($id)->admin->id)->update([
                        'owner' => 1,
                    ]);
                    Event::create([
                        'event' => User::find($id)->name ." (with id = ".$id.') , has prompted to Owner by '. auth()->user()->name,
                        'user_id' => auth()->user()->id,
                    ]);
                    return $this->createResponse(new UserResource(User::find($id)),'A new Owner added success');
                }
                return $this->processResponse();
            }
            return $this->processResponse();
        }
        return $this->errorResponse();
    }

    public function showAdmin(){
        if($users = Admin::get()) {
            $mat = [];
            $i = 0;
            foreach ($users as $user) {
                $mat[$i] = new UserResource($user->user);
                $i++;
            }
            return $this->successResponse($mat);
        }
        return $this->errorResponse();
    }

    public function search(Request $request){
        return $this->searchByModel(Admin::class ,$request);
    }

    public function userNotify(){
        $user_id = User::find(auth()->user()->id);
        $unread_notifications_count =
            $user_id->unreadnotifications->where('type' ,'App\Notifications\CreateUser')->count();
        $notifications_count =
            $user_id->notifications->where('type' ,'App\Notifications\CreateUser')->count();
        $unread_notifications =
            $user_id->unreadnotifications->where('type' ,'App\Notifications\CreateUser');
        $readed_notifications =
            $user_id->notifications
                ->where('type' ,'App\Notifications\CreateUser')
                ->where('read_at','!=' , null);

        $data = [
            'notification_count' => $notifications_count,
            'unread_notifications_count' => $unread_notifications_count,
            'unread_notifications' => $unread_notifications,
            'readed_notifications' => $readed_notifications
        ];
        return $this->successResponse($data);
    }

    public function deleteAdmin($id){
        if($user = User::find($id)){
            if (in_array($user, $this->admins())){
                Admin::destroy($user->admin->id);
                Event::create([
                    'event' => $user->name ." (with id = ".$user->id.")'s role has been changed to user by: ".auth()->user()->name,
                    'user_id' => auth()->user()->id,
                ]);
                return $this->deleteResponse($user->name . ' returned to user again');
            }
            return $this->errorValidateResponse('It is not an Admin');
        }
        return $this->errorResponse();
    }

}
