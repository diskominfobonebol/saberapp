<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShareBeritaResource extends JsonResource
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
            'pegawai_id' => optional($this->pegawai)->nama,
            'berita_id' => $this->berita_id,
            'berita_title' => $this->berita_title,
            'platform' => $this->platform,
            'url_berita' => $this->url_berita,
            'tanggal_share' => $this->tanggal_share,
        ];
    }
}
