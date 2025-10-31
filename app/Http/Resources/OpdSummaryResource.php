<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpdSummaryResource extends JsonResource
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
            'nama_opd' => $this->nama_opd,
            'pegawai' => SummaryOpdNotShareResource::collection($this->whenLoaded('pegawai')),
        ];
    }
}
