<?php

namespace App\Filament\Widgets;

use App\Models\ShareBerita;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ShareBeritaPerOpdChart extends ChartWidget
{
    protected static ?string $heading = '📊 Jumlah Share Berita per OPD';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // Ambil data jumlah share per OPD
        $data = ShareBerita::select(
            'opds.nama_opd',
            DB::raw('COUNT(share_beritas.id) as total_share')
        )
            ->join('pegawais', 'share_beritas.pegawai_id', '=', 'pegawais.id')
            ->join('opds', 'pegawais.opd_id', '=', 'opds.id')
            ->groupBy('opds.nama_opd')
            ->orderByDesc('total_share')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Share',
                    'data' => $data->pluck('total_share'),
                ],
            ],
            'labels' => $data->pluck('nama_opd'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true, // Memastikan grafik mulai dari 0
                    'ticks' => [
                        'precision' => 0,  // Kunci: Memaksa angka bulat (tanpa desimal)
                        // 'stepSize' => 1, // Opsional: Memaksa langkah minimal 1
                    ],
                ],
            ],
        ];
    }
}
