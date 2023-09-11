<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {
//        if( $this->admin != null ){
//            if($this->admin->owner){
//                // the User is Owner
//                $role = 2;
//            }
//            else{
//                // the User is Admin
//                $role = 1;
//            }
//        }else{
//            // it's Just a normal User
//            $role = 0;
//        }

        $role = $this->admin != null ? ($this->admin->owner ? 2 : 1) : 0;
        return [
            'id' => $this->id,
            'name' => $this ->name,
            'email' => $this->email,
            'role' =>$role ,
        ];
    }
}
