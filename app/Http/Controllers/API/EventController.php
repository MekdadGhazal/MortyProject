<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ResponseTrait;

    /**
     *  GET all events
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(){
        $data= Event::orderBy('created_at','desc')->get(['event', 'created_at']);
        return $this->successResponse($data);
    }

    /**
     * Get the event Created by User using ID
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserData($id){
        $data= Event::where('user_id' , $id)->orderBy('created_at','desc')->get(['event', 'created_at']);
        return $this->successResponse($data);
    }
}
