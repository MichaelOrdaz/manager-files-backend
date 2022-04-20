<?php

namespace App\Http\Resources;

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
            'nombre' => $this->nombre,
            'paterno' => $this->paterno,
            'materno' => $this->materno,
            'nombreCompleto' => "{$this->nombre} {$this->paterno} {$this->materno}",
            'celular' => $this->celular,
            'imagen' => $this->imagen ? asset("storage/{$this->imagen}") : $this->imagen,
            'departamento' => new DepartmentResource($this->departamento),
            'role' => $this->getRoleNames(),
        ];
    }
}