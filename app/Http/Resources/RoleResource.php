<?php

namespace App\Http\Resources;

use App\Helpers\Dixa;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $spanish = Dixa::SPANISH_ROLES;
        return [
            'id' => $this->id,
            'name' => $spanish[$this->name],
        ];
    }
}
