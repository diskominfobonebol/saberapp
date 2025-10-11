<?php

namespace App\Filament\Resources\ShareBeritaResource\Pages;

use App\Filament\Resources\ShareBeritaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShareBeritas extends ListRecords
{
    protected static string $resource = ShareBeritaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
