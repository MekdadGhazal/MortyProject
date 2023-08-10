<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ResponseTrait;
    public function getData(){
        $data= Event::orderBy('created_at','desc')->get(['event', 'created_at']);
        return $this->successResponse($data);
    }

    public function getUserData($id){
        $data= Event::where('user_id' , $id)->orderBy('created_at','desc')->get(['event', 'created_at']);
        return $this->successResponse($data);
    }
}
