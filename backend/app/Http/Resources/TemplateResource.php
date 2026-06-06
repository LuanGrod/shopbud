<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sectors' => $this->whenLoaded('sectors', fn () => $this->sectors->map(fn ($sector) => [
                'id' => $sector->id,
                'name' => $sector->name,
                'order' => $sector->order,
                'products' => $sector->products->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                ])->values(),
            ])->values()),
        ];
    }
}
