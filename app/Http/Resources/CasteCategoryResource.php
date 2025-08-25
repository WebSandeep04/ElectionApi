<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CasteCategoryResource extends JsonResource
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
            'description' => $this->description,
            'castes' => $this->whenLoaded('castes', function () {
                return $this->castes->map(function ($caste) {
                    return [
                        'id' => $caste->id,
                        'caste' => $caste->caste,
                    ];
                });
            }),
            'castes_count' => $this->whenLoaded('castes', function () {
                return $this->castes->count();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
