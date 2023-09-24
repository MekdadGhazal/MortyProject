<?php

namespace App\Http\Controllers\API\Courses;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseCommentResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\CourseComment;
use App\Models\CourseCommentReplay;
use Couchbase\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseCommentController extends Controller
{
    use ResponseTrait;

    /**
     *
     *  Show All comments
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id){
        if(Course::find($id)){
            if(Course::find($id)->comments->count()){
                return $this->successResponse(
                    CourseCommentResource::collection(
                        CourseComment::where('course_id',$id)->get()));
            }else{
                return $this->processResponse('there is no comment yet!');
            }
        }
        else{
         return $this->errorResponse();
        }
    }


    /**
     *  Insert Comment using Course ID
     * @param Request $request
     * @param $couser_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(Request $request , $couser_id){
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);
        if($validator->fails()){
            // status = 400
            return $this->errorValidateResponse($validator->errors());
        }
        $comment = CourseComment::create([
            'course_id' => $couser_id,
            'user_id' => auth()->user()->id,
            'content' => $request->comment,
        ]);
//        return $this->apiResponse($comment,201,'added successfully');
        return $this->createResponse(new CourseCommentResource($comment));
    }

    /**
     *  Destroy Comment using comment ID
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        try{
            if($comment = CourseComment::find($id)){
                $userId = $comment->user_id;
                if(auth()->user()->id != $userId){
                    return $this->errorResponse();
                }
                $comment->delete();
                return $this->deleteResponse();
            }
            return $this->errorResponse();
        }catch (\Exception $exception) {
            return $this->errorResponse();
        }
    }


    /**
     * ----------------------
     *      Replies
     * ----------------------
     */

    /**
     *
     *  Show all Replay using Comment ID
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function replies($id){
        $replies = CourseComment::find($id);
        if(! $replies->count()){
            return $this->processResponse();
        }
        $data = [];
        foreach ($replies->replies->sortByDesc('created_at') as $result){
            $data[] = [
                'replay' => $result,
                'user' => new UserResource(\App\Models\User::find($result->user_id))
            ];
        }
        return $data;
    }

    /**
     *
     *  Insert a new Replay using comment id
     * @param Request $request
     * @param $comment_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertReplay(Request $request , $comment_id){
        try {
            $validator = Validator::make($request->all(), [
                'replay' => 'required|string',
            ]);
            if($validator->fails()){
                // status = 400
                return $this->errorValidateResponse($validator->errors());
            }

            $replay_count = CourseComment::where('id' , $comment_id)->get('replay');
            CourseComment::where('id' , $comment_id)->update([
                'replay' =>  $replay_count[0]->replay + 1,
            ]);

            $comment = CourseCommentReplay::create([
                'comment_id' => $comment_id,
                'user_id' => auth()->user()->id,
                'content' => $request->replay,
            ]);

            $data = [
                'comment_id' => $comment_id,
                'content' => $request->replay,
                'replay' => 0,
                'repairs' =>[],
                'id' => $comment->id,
                'created_at' =>$comment ->created_at->diffForHumans(),
                'user' => new UserResource(auth()->user()),
            ];
            return $this->createResponse($data);
        }catch (\Exception $exception){
            return  $this->errorResponse();
        }
//        return $this->successResponse(new CourseCommentResource(CourseComment::find($comment_id)));
    }


    public function destroyReplay($id){
        try{
            if($replay = CourseCommentReplay::find($id)){
                $userId = $replay->user_id;
                if(auth()->user()->id != $userId){
                    return $this->errorResponse();
                }
                $replay->delete();
                return $this->deleteResponse();
            }
            return $this->errorResponse();
        }catch (\Exception $exception) {
            return $this->errorResponse();
        }
    }
}
