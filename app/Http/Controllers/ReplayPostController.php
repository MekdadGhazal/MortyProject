<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use App\Http\Resources\CourseCommentResource;
use App\Http\Resources\PostReplayResource;
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
//        return $replies->replies;
        if( $replies->replay == 0){
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

    public function insert(Request $request, $comment_id){
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
//        return $this->successResponse($comment);
        return $this->successResponse(new PostReplayResource(PostComment::find($comment_id)));
    }
}
