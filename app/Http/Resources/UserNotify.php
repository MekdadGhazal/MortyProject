<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotify extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
//            'id' => $this->data->id,
            'name' => $this->data['name'],
            'emil' => $this->data['email'],
            'time' => $this->created_at,
        ];
    }
}
