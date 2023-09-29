<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PostComment;
use App\Models\ReplayPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplayPostController extends Controller
{

    use ResponseTrait;

    public function replies($id){
        $replies = PostComment::find($id);
        if( $replies->replay == 0){
            return [];
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

    public function insert(Request $request, $comment_id){
        try {
            $validator = Validator::make($request->all(), [
                'replay' => 'required|string',
            ]);
            if($validator->fails()){
                // status = 400
                return $this->errorValidateResponse($validator->errors());
            }

            $replay_count = PostComment::find($comment_id)->get('replay');
            PostComment::find($comment_id)->update([
                'replay' =>  $replay_count[0]->replay + 1,
            ]);

            $comment= ReplayPost::create([
                'comment_id' => $comment_id,
                'user_id' => auth()->user()->id,
                'content' => $request->replay,
            ]);

            $data = [
                'replay' =>[
                    'id' => $comment->id,
                    'content' => $request->replay,
                    'comment_id' => $comment_id,
                    'user_id' =>auth()->user()->id,
                    'replay' => 0,
                    'repairs' =>[],
                    'created_at' =>$comment ->created_at->diffForHumans(),
                ],
                'user' => new UserResource(auth()->user()),
            ];

            return $this->createResponse($data);
        }catch (\Exception $exception){
            return $this->errorResponse();
        }
    }


    public function destroy($id){
        try{
            if($replay = ReplayPost::find($id)){
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
