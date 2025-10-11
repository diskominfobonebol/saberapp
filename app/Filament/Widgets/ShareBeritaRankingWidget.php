<?php

namespace App\Filament\Widgets;

use App\Models\ShareBerita;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ShareBeritaRankingWidget extends ChartWidget
{
    protected static ?string $heading = '🏆 Ranking Share Berita Terbanyak (Minggu Ini)';

    protected function getData(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $data = ShareBerita::select('pegawai_id', DB::raw('COUNT(*) as total_share'))
            ->whereBetween('tanggal_share', [$startOfWeek, $endOfWeek])
            ->groupBy('pegawai_id')
            ->orderByDesc('total_share')
            ->with('pegawai')
            ->take(10)
            ->get();

        $labels = $data->pluck('pegawai.nama')->toArray();
        $values = $data->pluck('total_share')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Share',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
