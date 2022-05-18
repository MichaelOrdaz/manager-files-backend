<?php

namespace App\Http\Resources;

use App\Helpers\Dixa;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'second_lastname' => $this->second_lastname,
            'fullName' => "{$this->name} {$this->lastname} {$this->second_lastname}",
            'phone' => $this->phone,
            'image' => $this->image ? asset("storage/{$this->image}") : $this->image,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'role' => $this->getRoleNames()->map(fn ($rol) => Dixa::SPANISH_ROLES[$rol])
        ];
    }
}
