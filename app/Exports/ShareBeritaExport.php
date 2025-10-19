<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShareBeritaExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Pegawai',
            'NIP',
            'OPD',
            'Berita Title',
            'Platform',
            'URL Berita',
            'Tanggal Share',
        ];
    }

    public function map($shareBerita): array
    {
        return [
            $shareBerita->pegawai->nama,
            "'" . $shareBerita->pegawai->nip,
            $shareBerita->pegawai->opd->nama_opd,
            $shareBerita->berita_title,
            $shareBerita->platform,
            $shareBerita->url_berita,
            $shareBerita->tanggal_share,
        ];
    }
}
