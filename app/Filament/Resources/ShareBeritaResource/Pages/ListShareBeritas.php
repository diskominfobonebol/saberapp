<?php

namespace App\Filament\Resources\ShareBeritaResource\Pages;

use App\Exports\ShareBeritaExport;
use App\Exports\SharesByOpdExport;
use App\Exports\SharesByPegawaiExport;
use App\Exports\SharesByPlatformExport;
use App\Filament\Resources\ShareBeritaResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListShareBeritas extends ListRecords
{
    protected static string $resource = ShareBeritaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Ekspor per OPD
            Action::make('export_by_opd')
                ->label('Export Laporan')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    // Ini adalah kuncinya: 
                    // Mengambil query tabel yang sudah terfilter
                    $query = $this->getFilteredTableQuery();

                    return Excel::download(
                        new ShareBeritaExport($query),
                        'laporan_share_per_opd_' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),

        ];
    }
}
