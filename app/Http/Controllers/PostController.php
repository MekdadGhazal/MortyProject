<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ApiTrait\FunctionTemplateTrait;
use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use ResponseTrait;
    use FunctionTemplateTrait;

    public function index(){
        try {
            $posts = Post::get()->sortByDesc('created_at');
            if($posts){
                return $this->successResponse(PostResource::collection($posts));
            }
            return $this->errorResponse();
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }

    public function getPost($id){
        try {
            $post = Post::find($id);
            if($post){
                return $this->successResponse(new PostResource($post));
            }
            return $this->errorResponse();
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }

    public function insert(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:50',
                'body'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->errorValidateResponse($validator->errors());
            }
            $post = Post::create(array_merge($validator->validated(),[
                'user_id' => auth()->user()->id,
            ]));
            return $this->createResponse($post);
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }

    public function deletePost(Request $request){
        try {
            $post = Post::find($request->id);
            if($post){
                if($post->user_id == auth()->user()->id) {
                    $post->delete();
                    return $this->deleteResponse();
                }
                return $this->unAuthorizedResponse();
            }
            return $this->errorResponse();
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }

    public function editPost(Request $request){
        try {
            $post = Post::find($request->post_id);
            if($post){
                if($post->user_id == auth()->user()->id) {
                    $validator = Validator::make($request->all(), [
                        'title' => 'required|string|max:50',
                        'body'=>'required|string',
                    ]);
                    if($validator->fails()){
                        return $this->errorValidateResponse($validator->errors());
                    }
                    $post->update($validator->validated());
                    return $this->createResponse($post,'Uploaded Successfully.');
                }
                return $this->unAuthorizedResponse();
            }
            return $this->errorResponse();
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }

}
