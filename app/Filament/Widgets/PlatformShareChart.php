<?php

namespace App\Filament\Widgets;

use App\Models\ShareBerita;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PlatformShareChart extends ChartWidget
{
    protected static ?string $heading = '📊 Platform Share Berita Terpopuler';

    // Biar tampil full lebar
    protected int|string|array $columnSpan = 'full';

    // Tinggi chart biar proporsional
    protected static ?int $height = 350;

    protected function getData(): array
    {
        $data = ShareBerita::select('platform', DB::raw('COUNT(*) as total'))
            ->groupBy('platform')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Share',
                    'data' => $data->pluck('total'),
                    'backgroundColor' => [
                        '#25D366', // WhatsApp
                        '#1877F2', // Facebook
                        '#E4405F', // Instagram
                        '#0088cc', // Telegram
                        '#FFBB00', // Email
                    ],
                ],
            ],
            'labels' => $data->pluck('platform')->map(function ($platform) {
                return ucfirst($platform);
            }),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
