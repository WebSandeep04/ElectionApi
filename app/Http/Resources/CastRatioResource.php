<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CastRatioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'caste_ratio_id' => $this->caste_ratio_id,
            'loksabha_id' => $this->loksabha_id,
            'vidhansabha_id' => $this->vidhansabha_id,
            'block_id' => $this->block_id,
            'panchayat_choosing_id' => $this->panchayat_choosing_id,
            'panchayat_id' => $this->panchayat_id,
            'village_choosing_id' => $this->village_choosing_id,
            'village_id' => $this->village_id,
            'booth_id' => $this->booth_id,
            'caste_id' => $this->caste_id,
            'caste_ratio' => $this->caste_ratio,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'loksabha' => $this->whenLoaded('lokSabha', function () {
                return [
                    'id' => $this->lokSabha->id,
                    'loksabha_name' => $this->lokSabha->loksabha_name,
                ];
            }),
            'vidhansabha' => $this->whenLoaded('vidhanSabha', function () {
                return [
                    'id' => $this->vidhanSabha->id,
                    'vidhansabha_name' => $this->vidhanSabha->vidhansabha_name,
                ];
            }),
            'block' => $this->whenLoaded('block', function () {
                return [
                    'id' => $this->block->id,
                    'block_name' => $this->block->block_name,
                ];
            }),
            'panchayat' => $this->whenLoaded('panchayat', function () {
                return [
                    'id' => $this->panchayat->id,
                    'panchayat_name' => $this->panchayat->panchayat_name,
                ];
            }),
            'panchayat_choosing_data' => $this->whenLoaded('panchayatChoosing', function () {
                return [
                    'id' => $this->panchayatChoosing->id,
                    'name' => $this->panchayatChoosing->name,
                    'status' => $this->panchayatChoosing->status,
                ];
            }),
            'village_choosing_data' => $this->whenLoaded('villageChoosing', function () {
                return [
                    'id' => $this->villageChoosing->id,
                    'name' => $this->villageChoosing->name,
                    'status' => $this->villageChoosing->status,
                ];
            }),
            'village_data' => $this->whenLoaded('village', function () {
                return [
                    'id' => $this->village->id,
                    'village_name' => $this->village->village_name,
                ];
            }),
            'booth_data' => $this->whenLoaded('booth', function () {
                return [
                    'id' => $this->booth->id,
                    'booth_name' => $this->booth->booth_name,
                ];
            }),
            'caste' => $this->whenLoaded('caste', function () {
                return [
                    'id' => $this->caste->id,
                    'caste_name' => $this->caste->caste,
                ];
            }),
        ];
    }
}
