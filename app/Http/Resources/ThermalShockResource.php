<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThermalShockResource extends JsonResource
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
            'hari_tgl' => $this->hari_tgl,
            'oven' => $this->oven,
            'pintu' => $this->pintu,
            'suhu_testing' => $this->suhu_testing,
            'suhu_motor' => $this->suhu_motor,
            'suhu_display' => $this->suhu_display,
            'suhu_actual' => $this->suhu_actual,
            'jam_awal_proses' => $this->jam_awal_proses,
            'jam_capai_suhu' => $this->jam_capai_suhu,
            'suhu_awal' => $this->suhu_awal,
            'suhu_air' => $this->suhu_air,
            'jam_mulai_tembak' => $this->jam_mulai_tembak,
            'jam_selesai_tembak' => $this->jam_selesai_tembak,
            'details_count' => $this->whenCounted('details'),
            'details' => ThermalShockDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
