<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VidhanSabhaResource extends JsonResource
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
            'loksabha_id' => $this->loksabha_id,
            'vidhansabha_name' => $this->vidhansabha_name,
            'vidhan_status' => $this->vidhan_status,
            'lok_sabha' => $this->whenLoaded('lokSabha', function () {
                return new LokSabhaResource($this->lokSabha);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
