<?php

namespace App\Filament\Widgets;

use App\Models\ShareBerita;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ShareBeritaTrendChart extends ChartWidget
{
    protected static ?string $heading = '📈 Tren Kenaikan Jumlah Share Berita';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // Ambil data share berita per hari selama 30 hari terakhir
        $data = ShareBerita::select(
            DB::raw('DATE(tanggal_share) as tanggal'),
            DB::raw('COUNT(*) as total_share')
        )
            ->where('tanggal_share', '>=', Carbon::now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $labels = $data->pluck('tanggal')->map(function ($date) {
            return Carbon::parse($date)->translatedFormat('d M');
        });

        $values = $data->pluck('total_share');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Share',
                    'data' => $values,
                    'fill' => false,
                    'tension' => 0.3, // bikin garis agak halus
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
