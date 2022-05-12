<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoricalResource extends JsonResource
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
            'date' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'action' => new ActionResource($this->whenLoaded('action')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
