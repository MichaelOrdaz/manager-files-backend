<?php

namespace App\Http\Resources;

use App\Helpers\Dixa;
use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $identifier = $this->min_identifier;
        if (!empty($this->max_identifier)) {
            $identifier .= '-' . $this->max_identifier;
        }

        if ($this->type->name === Dixa::FOLDER) {
            $url = route('downloadFolder', ['document_id' => $this->id]);
        } else {
            $url = asset(
                "storage" . 
                DIRECTORY_SEPARATOR . 
                Dixa::PATH_FILES . 
                DIRECTORY_SEPARATOR . 
                $this->location
            );
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'url' => $url,
            'description' => $this->description,
            'identifier' => $identifier,
            'tags' => $this->tags,
            'type' => new DocumentTypeResource($this->whenLoaded('type')),
            'date' => $this->date,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'department' => $this->whenLoaded('department'),
            'parent' => $this->whenLoaded('parent'),
            'historical' => HistoricalResource::collection($this->whenLoaded('historical')),
            'share' => UserResource::collection($this->whenLoaded('share')),
            'permission' => $this->whenPivotLoaded('document_user', function () {
                return $this->pivot->permission;
            }),
        ];
    }
}