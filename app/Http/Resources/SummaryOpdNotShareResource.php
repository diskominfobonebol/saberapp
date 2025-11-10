<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryOpdNotShareResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>p
     */
    public function toArray(Request $request): array
    {
        return [
            'nama' => $this->nama,
            'nip' => $this->nip,
        ];
    }
}
