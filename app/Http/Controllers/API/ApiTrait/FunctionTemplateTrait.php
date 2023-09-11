<?php

namespace App\Http\Controllers\API\ApiTrait;

use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Event;
use App\Models\User;
use App\Notifications\CreateUser;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

trait FunctionTemplateTrait
{

    public function admins(){
        $users = User::get();
        $admins = Admin::get('user_id');
        $mat = [];
        $i = 0;
        foreach ($users as $user) {
            foreach ($admins as $id) {
                if ($user->id == $id->user_id) {
                    $mat[$i] = $user;
                    $i++;
                    break;
                }
            }
        }
        return $mat;
    }
    /**
     *  Search Function
     *
     * @param $model
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByModel($model, $request): \Illuminate\Http\JsonResponse
    {
        $search = User::where('name','LIKE', '%' . $request->search . '%')
            ->orwhere ('email','LIKE', '%' . $request->search . '%')
            ->orwhere ('created_at', 'LIKE','%' . $request->search . '%')
            ->get();
        if ($model == 'App\\Models\\User'){
            $search = UserResource::collection($search);
            return $this->apiResponse($search, 201, 'ok');
        } else{
            $admins = Admin::get('user_id');
            $mat = [];
            $i = 0;
            foreach ($search as $user){
                foreach ($admins as $id){
                    if($user->id ==  $id->user_id){
                        $mat[$i] = new UserResource($user);
                        $i++;
                        break;
                    }
                }
            }
            return $this-> apiResponse($mat, 201, 'ok');
        }
    }

    /**
     * Get a JWT via given credentials.
     */
    public function  loginUser($request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $this->notValidResponse($validator->errors());
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return $this->unAuthorizedResponse();
        }
        return $this->createNewToken($token);
    }

    /**
     *  Register
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function RegisterUser($request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            /**
             *  to add a new Validate to Register :
             *  should contain
             *  1. lowercase letter :: (?=.*[a-z])
             *  2. Uppercase letter :: (?=.*[A-Z])
             *  3. number :: (?=.*[0-9])
             *  4. unique character [!,@,#,$,%] :: (?=.*[!@#$%])
             *
             *  expiration :: add the following code in password check ::
             *  'regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%]).*$/'
             *
             *  to make it contain at least one character just : upper or lower or unique
             *  code :: 'regex:/^.*(?=.*[a-zA-Z!@#$%]).*$/'
             */
        ]);
        if($validator->fails()){
            // status = 400
            return $this->errorValidateResponse($validator->errors());
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => $request->password,
//        ]);

        // send Notifications for all admin
        $admins = $this->admins();
        $user_id = $user['id'];
        $user_name = $user['name'];
        $user_eamil = $user ['email'];
        Notification::send($admins, new CreateUser($user_id, $user_name, $user_eamil));

        return $this->apiResponse(new UserResource($user),201,'User successfully registered');
    }

    /**
     * Log the user out (Invalidate the token)
     */
    public function logoutUser(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Register a User.
     * @throws \Illuminate\Validation\ValidationException
     */
    public function insertUserData($request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return $this->notValidResponse($validator->errors());
        }
        if(auth()->user()){
            $user = User::find(auth()->user()->id);
            $user->update(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));
            Event::create([
                'event' => 'The User with id:' .  auth()->user()->id . ', has changed his Profile Information',
            ]);
            return $this->successResponse($user); //201
        }
        return $this->errorResponse();
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        $data = [
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user_info' => new UserResource(auth()->user()),
        ];
        return $this->successResponse($data);
    }

    /**
     * @param $request
     * @param $key
     * @param $option
     * @param $storeIn
     * @return mixed
     */
    public function upload($request, $inputFileName, $optionFileSystem, $storeIn = null): mixed
    {
        $file = $request->file($inputFileName)->getClientOriginalName();
        $path = $request->file($inputFileName)->storeAs($storeIn ,$file, $optionFileSystem);
        return $path;
    }
}
