<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\countOf;

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
            'id' => $this->data['id'],
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'created_at' => $this->created_at->diffForHumans(),
            'verify' => isset($this->read_at)?$this->read_at->diffForHumans():"Not Yet",
            'verifyBy' => strlen($this->data['by'])?$this->data['by'] : "-",
        ];
    }
}
