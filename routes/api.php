<?php

use App\Http\Controllers\API\Admin\AdminController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\Controller\AuthController;
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
     *  and the notifications of register will be readed in all Admins
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
    Route::get('/events', [\App\Http\Controllers\EventController::class, 'getData']);

    /**
     *  11. Show Events for User by ID
     */
    Route::get('/events/{id}', [\App\Http\Controllers\EventController::class, 'getUserData'])->middleware('owner.check');

    /**
     *  12. Delete Admin (JUST Owner)
     */
    Route::get('delete/{id}', [AdminController::class,'deleteAdmin'])->middleware(middleware: 'owner.check');

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
    Route::get('/edit',[UserController::class,'getData'])->middleware('verify.check');
    Route::post('/insert',[UserController::class,'insertData'])->middleware('verify.check');

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
    
});




