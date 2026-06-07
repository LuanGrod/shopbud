<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'template_id' => $this->template_id,
            'expires_at' => $this->expires_at?->toJSON(),
            'snapshot' => $this->snapshot,
        ];
    }
}
