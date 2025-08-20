<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillageResource extends JsonResource
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
            'panchayat_id' => $this->panchayat_id,
            'village_choosing' => $this->village_choosing,
            'village_name' => $this->village_name,
            'village_status' => $this->village_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'lok_sabha' => $this->whenLoaded('lokSabha', function () {
                return new LokSabhaResource($this->lokSabha);
            }),
            'vidhan_sabha' => $this->whenLoaded('vidhanSabha', function () {
                return new VidhanSabhaResource($this->vidhanSabha);
            }),
            'block' => $this->whenLoaded('block', function () {
                return new BlockResource($this->block);
            }),
            'panchayat' => $this->whenLoaded('panchayat', function () {
                return new PanchayatResource($this->panchayat);
            }),
        ];
    }
}
