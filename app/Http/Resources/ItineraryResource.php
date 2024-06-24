<?php

namespace App\Http\Resources;

use App\Models\Place;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'places' => PlaceResource::collection($this->whenLoaded('places')),
        ];
    }
}
