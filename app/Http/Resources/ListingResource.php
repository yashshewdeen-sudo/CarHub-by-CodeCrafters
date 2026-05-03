<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'make'         => $this->make,
            'model'        => $this->model,
            'year'         => $this->year,
            'mileage'      => $this->mileage,
            'price'        => (float) $this->price,
            'fuel_type'    => $this->fuel_type,
            'transmission' => $this->transmission,
            'condition'    => $this->condition_status,
            'status'       => $this->status,
            'description'  => $this->description,
            'seller'       => $this->whenLoaded('seller', fn () => [
                'id'   => $this->seller->id,
                'name' => $this->seller->name,
            ]),
            'category'     => $this->whenLoaded('category', fn () => $this->category?->name),
            'tags'         => $this->whenLoaded('tags', fn () => $this->tags->pluck('name')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
