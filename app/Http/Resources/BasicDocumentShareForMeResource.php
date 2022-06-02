<?php

namespace App\Http\Resources;

use App\Helpers\Dixa;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicDocumentShareForMeResource extends JsonResource
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
            'type' => new DocumentTypeResource($this->whenLoaded('type')),
            'url' => $url,
            'date' => $this->date,
            'sons_count' => $this->when(isset($this->sons_count), $this->sons_count),
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'parent' => $this->whenLoaded('parent'),
            'department' => $this->whenLoaded('department'),
            'permission' => $this->pivot->permission ?? $this->permission,
            'grantedBy' => $this->pivot->granted_by ?? $this->granted_by,
            'creator' => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
