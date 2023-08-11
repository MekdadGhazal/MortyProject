<?php

use App\Http\Controllers\API\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/events/{id}', [\App\Http\Controllers\EventController::class, 'getUserData']);

//Route::get('/register' ,function (){
//    return view('register');
//});
//
//Route::post('/sendNot' ,[\App\Http\Controllers\API\User\UserController::class,'register'])->name('register');

//Route::get('delete/{id}', [AdminController::class,'deleteAdmin']);
Route::get('course/{id}', [\App\Http\Controllers\CourseController::class,'index']);
Route::get('teacher/{id}', [\App\Http\Controllers\CourseController::class,'courses']);
Route::get('course.info/{id}', [\App\Http\Controllers\CourseController::class,'fullCourseInfo']);
Route::get('user/course/{id}', [\App\Http\Controllers\CourseController::class,'userCourses']);


