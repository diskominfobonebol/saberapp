<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'nip' => $this->nip,
            'nama' => $this->name,
            'pegawai_id' => $this->pegawai->id ?? null,
            'opd' => $this->pegawai->opd->nama_opd ?? null,
        ];
    }
}
