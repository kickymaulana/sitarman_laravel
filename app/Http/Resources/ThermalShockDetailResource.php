<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThermalShockDetailResource extends JsonResource
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
            'thermal_shock_id' => $this->thermal_shock_id,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->whenLoaded('customer', function () {
                return $this->customer->customer;
            }),
            'modelsize_id' => $this->modelsize_id,
            'modelsize_name' => $this->whenLoaded('modelSize', function () {
                return $this->modelSize->modelsize;
            }),
            'oven_kode_tanah' => $this->oven_kode_tanah,
            'spesifikasi' => $this->spesifikasi,
            'berat_former' => $this->berat_former,
            'tanggal_keluar_oven' => $this->tanggal_keluar_oven,
            'tgl_produksi' => $this->tgl_produksi,
            'posisi_former' => $this->posisi_former,
            'hasil_test' => $this->hasil_test,
            'keterangan' => $this->keterangan,
        ];
    }
}
