<?php

use App\Http\Controllers\API\Admin\AdminController;
use App\Http\Controllers\API\Courses\CourseCommentController;
//use App\Http\Controllers\API\Courses\CourseController;
use App\Http\Controllers\API\Courses\CourseController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\User\UserController;
//use App\Http\Controllers\Controller\AuthController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
//Route::group([
//    'middleware' => 'api',
//    'prefix' => 'auth'
//], function ($router) {
//    Route::post('/login', [AuthController::class, 'login'])->name('login');
//    Route::post('/register', [AuthController::class, 'register']);
//    Route::post('/logout', [AuthController::class, 'logout']);
//    Route::post('/refresh', [AuthController::class, 'refresh']);
//    Route::get('/user-profile', [AuthController::class, 'userProfile']);
//});

//-----------------------------------------------------------------------------------------------
/**
 *  ADMIN Routes ::
 *  1. Show All Users
 *  2. Show User by ID
 *  3. Verify User
 *  4. Delete User
 *  5. Prompt User To Admin
 *  6. Prompt User To become an Owner And Admin (if it is not)
 *  7. Show All Admins
 *  8. Search for Other Admins
 *  9. notification for new user registration
 *  10. Event List
 *  11. Show Events for User by ID (Owner JUST)
 *  12. Delete Admin
 *  13. GET courses' name that joined by user
 *  14. GET the Full Information of course
 *  15. GET information of course/s that created by a Teacher [user] using user_id
 *  16. GET users' name that joined to course with course_id
 *  17. Create a course
 *  18. Update a course info
 *  19. Add Videos To course using course id
 *
 *  note that ::
 *  each route should start with [api/admin]
 */

Route::group([
    'middleware' => ['auth:api', 'admin.check'],
    'prefix' => 'admin',
] ,function (){
    /**
     *  1. Show All Users
     */
    Route::get( '/users' , [AdminController::class , 'index']);

    /**
     *  2. Show User By ID
     */
    Route::get('/user/{id}',[AdminController::class,'getUser']);

    /**
     *  3. Verify User To make it enter The Website
     *  and the notifications of register will be read by all Admins
     */
    Route::post('/user/verify/{id}',[AdminController::class,'verifyUser']);

    /**
     *  4. Delete User Account - policy -
     */
    Route::delete('/user/delete/{id}',[AdminController::class,'deleteUser']);

    /**
     *  5. Prompt a User to become as a new Admin
     *
     *  note that::
     *  You should be an Owner to add a new Admin
     *  using middleware : owner.check
     */
    Route::post('/user/prompt/{id}',[AdminController::class,'prompt'])->middleware('owner.check');

    /**
     *  6.
     *  Prompt an Admin to become an Owner to the Website
     *  or
     *  Prompt a User to become an Owner to the Website and Admin
     *
     *      note that::
     *      1) the owner is admin also
     *      2) there is one owner of website
     *
     */
    Route::post('/user/owner/{id}',[AdminController::class,'owner'])->middleware('owner.check');

    /**
     *  7. Show all Others Admin
     */
    Route::get('/admins',[AdminController::class,'showAdmin']);

    /**
     *  8. Search for Other Admins
     */
    Route::post('/search' , [AdminController::class , 'search']);

    /**
     *  9. notification ::
     *      Get all notifications about a new User registered using auth_user_id
     */
    Route::get('/notify/users', [AdminController::class, 'userNotify']);

    /**
     *  10. Event List
     */
    Route::get('/events', [EventController::class, 'getData']);

    /**
     *  11. Show Events for User by ID
     */
    Route::get('/events/{id}', [EventController::class, 'getUserData'])->middleware('owner.check');

    /**
     *  12. Delete Admin (JUST Owner)
     */
    Route::get('/delete/{id}', [AdminController::class,'deleteAdmin'])->middleware(middleware: 'owner.check');

    /**
     *  13. GET courses' name that joined by user with user_id
     *  معرفة اسماء الكورسات التي ينضم اليها يوزر باستخدام الid
     */
    Route::get('/user/course/{id}', [CourseController::class,'userCourses']);

    /**
     *  14. GET the Full Information of course:
     *      1) the course Info
     *      2) created by
     *      3) the members
     */
    Route::get('/course.info/{id}', [CourseController::class,'fullCourseInfo']);

    /**
     *  15. GET information of course/s that created by a Teacher [user] using user_id
     */
    Route::get('/teacher/{id}', [CourseController::class,'courses']);

    /**
     *  16. GET users' name that joined to course with course_id
     */
    Route::get('/course/{id}',[CourseController::class,'findCourse']);

    /**
     *  17.Insert a course
     */
    Route::post('/course/insert',[CourseController::class,'insert']);

    /**
     *  18.Update a course Info
     */
    Route::post('/course/edit',[CourseController::class,'edit']);

    /**
     *  19. add Videos To course using course id
     */
    Route::post('/course/add-video',[CourseController::class,'addVideo']);

    /**
     *  20.New Registration
     */
    Route::get('/new',[UserController::class,'Registeration']);


});

//-----------------------------------------------------------------------------------------------
/**
 *  USER Routes ::
 *  1. Login
 *  2. Logout
 *  3. Edit information
 *      3.1 Get Data
 *      3.2 Post Data
 *  4. Delete Account
 *  5. Search for Other Users
 *  6. register
 *  7. knowing the Course that joined //need to remove id and edition
 *  8. Get Information about course
 *  9. Get Notifications about Courses
 *  10. Add a Comment on Course
 *  11. Delete a Comment
 *  12. Show all Comments
 *  13.add Replay on comment
 *  14.Show all replies for comment
 *  15.See all video of course
 *  16.Get all Courses
 *  17. search for courses [Advanced]
 *  18.Show a video from notification
 *
 *
 *  note that ::
 *  each route should start with [api/user]
 *  Just the Route no.3 need verify The account to continue...
 *  all Route Does not required Token [middleware => auth:api ]
 */

Route::group([
    'middleware' => ['api'] ,
    'prefix' => 'user'
],function (){
    /**
     *  1. Login and Generate a Token
     */
    Route::post('/login',[UserController::class,'login'])->name('login');

    /**
     *   2. Logout and Delete the Token
     */
    Route::post('/logout',[UserController::class,'logout']);

    /**
     *  3. Edit information
     *      3.1 Get User information - using Get method
     *      3.2 Edit the New information - using Post method
     */
    Route::get('/edit',[UserController::class,'getData'])->middleware(['verify.check', 'auth:api']);
    Route::post('/insert',[UserController::class,'insertData'])->middleware('verify.check', 'auth:api');

    /**
     *  4. Delete Account
     */
    Route::post('/delete',[UserController::class,'destroy']);

    /**
     *  5. Search for Other Users
     */
    Route::post('/search' , [UserController::class , 'search']);

    /**
     *  6. Register new User
     */
    Route::post('/register' , [UserController::class, 'register']);

    /**
     *  7. knowing the Course that joined
     */
    Route::get('/course/{id}', [CourseController::class,'userCourses'])->middleware( 'auth:api');

    /**
     *  8. Get Information about course
     */
    Route::get('/course/info/{id}' , [CourseController::class,'getData']);

    /**
     *  9. Get Notifications about Courses
     */
    Route::get('/notifications' , [CourseController::class,'notifyMe'])->middleware(['notify.check', 'auth:api']);

    /**
     *  10. Comment on Course
     */
    Route::post('/course/{id}/insert-comment' , [CourseCommentController::class ,'insert']);

    /**
     *  11. Delete a Comment
     */
    Route::delete('/course/{id}/destroy-comment' , [CourseCommentController::class ,'destroy'])->middleware('auth:api');

    /**
     *  12. Show all Comments
     */
    Route::get('/course/{id}/comments' , [CourseCommentController::class ,'index']);

    /**
     *  13.add Replay on comment
     */
    Route::post('/course/replay-comment/{id}' , [CourseCommentController::class ,'insertReplay']);

    /**
     *  14.Show all replies for comment
     */
    Route::get('/course/replay-comment/{id}' , [CourseCommentController::class ,'replies']);

    /**
     *  15.See all video of course
     */
    Route::get('/course/{id}/videos',[CourseController::class,'showVideos']);

    /**
     *  16. all courses
     */
    Route::get('/courses', [CourseController::class,'index']);

    /**
     *  17. search for courses [Advanced]
     */
    Route::get('/search-uc', [CourseController::class,'search']);

    /**
     *  18.Show a video from notification
     */
    Route::get('/video/{id}',[CourseController::class,'showVideo']);

});
