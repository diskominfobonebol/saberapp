<?php

namespace App\Filament\Widgets;

use App\Models\ShareBerita;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ShareBeritaRankingWidget extends ChartWidget
{
    protected static ?string $heading = '📊 Rekap Share Berita Per Hari (Berdasarkan OPD)';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // Ambil bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Ambil semua tanggal dalam bulan ini
        $dates = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        // Ambil data share per opd per tanggal
        $data = ShareBerita::join('pegawais', 'share_beritas.pegawai_id', '=', 'pegawais.id')
            ->join('opds', 'pegawais.opd_id', '=', 'opds.id')
            ->whereBetween('share_beritas.tanggal_share', [$startOfMonth, $endOfMonth])
            ->select(
                'opds.nama_opd',
                DB::raw('DATE(share_beritas.tanggal_share) as tanggal'),
                DB::raw('COUNT(share_beritas.id) as total_share')
            )
            ->groupBy('opds.nama_opd', 'tanggal')
            ->orderBy('tanggal')
            ->get();

        // Kelompokkan data berdasarkan OPD
        $grouped = $data->groupBy('nama_opd');

        // Siapkan dataset chart per OPD
        $datasets = [];
        foreach ($grouped as $opd => $records) {
            $dailyShares = [];
            foreach ($dates as $d) {
                $found = $records->firstWhere('tanggal', $d);
                $dailyShares[] = $found ? $found->total_share : 0;
            }

            $datasets[] = [
                'label' => $opd,
                'data' => $dailyShares,
                'fill' => false,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $dates->map(fn($d) => Carbon::parse($d)->format('d'))->toArray(), // tampilkan hanya tanggal di sumbu X
        ];
    }

    protected function getType(): string
    {
        return 'line'; // bisa diubah ke 'bar' kalau mau batang per hari
    }
}
