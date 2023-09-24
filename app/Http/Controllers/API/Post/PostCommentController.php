<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostReplayResource;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostCommentController extends Controller
{
    use ResponseTrait;

    /**
     *  param like :: /comments?id=8
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request){
        try {
            $post = Post::find($request->id);
            if($post){
                $comments = PostComment::where('post_id', $request->id)->get();
                return $this->successResponse(
                        PostReplayResource::collection($comments)
                    );
            }
            return $this->notValidResponse();
        }catch (\Exception $e){
            return  $this->errorResponse();
        }
    }


    public function insert(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'content'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->errorValidateResponse($validator->errors());
            }
            $comment = PostComment::create(array_merge($validator->validated(),[
                'user_id' => auth()->user()->id,
                'post_id' => $request->post_id,
            ]));
            return $this->createResponse(new PostReplayResource($comment));
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function destroy(Request $request)
    {
        try {
            $comment = PostComment::find($request->post_id);
            if($comment){
                if($comment->user_id == auth()->user()->id) {
                    $comment->delete();
                    return $this->deleteResponse();
                }
                return $this->unAuthorizedResponse();
            }
            return $this->errorResponse();
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }


}
