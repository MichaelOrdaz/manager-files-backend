<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasicDocumentResource extends JsonResource
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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'type' => new DocumentTypeResource($this->whenLoaded('type')),
            'date' => $this->date,
            'sons_count' => $this->when(isset($this->sons_count), $this->sons_count),
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'parent' => $this->whenLoaded('parent')
        ];
    }
}
