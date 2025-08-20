<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PanchayatResource extends JsonResource
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
            'vidhansabha_id' => $this->vidhansabha_id,
            'block_id' => $this->block_id,
            'panchayat_choosing' => $this->panchayat_choosing,
            'panchayat_name' => $this->panchayat_name,
            'panchayat_status' => $this->panchayat_status,
            'lok_sabha' => $this->whenLoaded('lokSabha', function () {
                return new LokSabhaResource($this->lokSabha);
            }),
            'vidhan_sabha' => $this->whenLoaded('vidhanSabha', function () {
                return new VidhanSabhaResource($this->vidhanSabha);
            }),
            'block' => $this->whenLoaded('block', function () {
                return new BlockResource($this->block);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
