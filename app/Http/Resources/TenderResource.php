<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
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
            'external_code' => $this->external_code,
            'number' => $this->number,
            'status' => $this->status,
            'name' => $this->name,
            'updated_at' => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
