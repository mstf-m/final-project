<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone_number' => $this->phone_number,
            'avatar_url' => $this->avatar_url,
            'gender' => $this->gender,
            'birthday' => $this->birthday ? (is_string($this->birthday) ? $this->birthday : $this->birthday->format('Y-m-d')) : null,
            'bio' => $this->bio,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'activities_count' => $this->whenCounted('activities'),
            'participations_count' => $this->whenCounted('participants'),
        ];
    }
}