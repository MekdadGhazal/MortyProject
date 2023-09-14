<?php

namespace App\Http\Controllers\API\ApiTrait;

trait ResponseTrait
{
    /**
     *  Response :: API
     * @param $data
     * @param $status
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse($data, $status, $message){
        $matrix = [
            'data' => $data,
            'status' => $status,
            'message'=>$message,
        ];
        return response()->json($matrix);
    }

    /**
     *  status = 200
     *  1. Delete
     *  2. GET
     */
    public function deleteResponse($data = null, $message = 'Deleted Successfully'){
        return $this->apiResponse($data, 200 , $message);
    }
    public function successResponse($data){
        return $this->apiResponse($data, 200, 'OK');
    }

    /**
     *  status = 201
     *  1. POST
     *  2. PUT
     *
     *  Create a new Row in DB
     */
    public function createResponse($data, $message = 'Created Successfully'){
        return $this->apiResponse($data, 201 , $message);
    }

    /**
     * status = 202
     * The request has been received but not yet acted upon.
     */
    public function processResponse($message = "Nothing to do"){
        return $this->apiResponse($message, 202 , "Accepted");
    }

    /**
     * status = 400
     * The server cannot or will not process the request due to something that is perceived to be a client error.
     */
    public function errorValidateResponse($error){
        return $this->apiResponse($error, 400 ,'Bad Request');
    }

    /**
     *  status = 401
     *  The client must authenticate itself to get the requested response
     */
    public function unAuthorizedResponse (){
        return $this->apiResponse(null, 401, 'Unauthorized');
    }

    /**
     *  status = 404
     *  The server cannot find the requested resource
     */
    public function errorResponse(){
        return $this->apiResponse(null, 404, "NOT FOUND");
    }

    /**
     *  status = 422
     *  The request was well-formed but was unable to be followed due to semantic errors
     */
    public  function notValidResponse($message = 'Unprocessable Content'){
        return $this->apiResponse(null, 422 , $message);
    }

}
