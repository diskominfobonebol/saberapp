<?php

namespace App\Http\Resources\Pegawai;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nip' => $this->nip,
            'nama' => $this->nama,
            'opd' => optional($this->user->pegawai->opd)->nama_opd,
        ];
    }
}
