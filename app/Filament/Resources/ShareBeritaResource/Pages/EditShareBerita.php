<?php

namespace App\Filament\Resources\ShareBeritaResource\Pages;

use App\Filament\Resources\ShareBeritaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShareBerita extends EditRecord
{
    protected static string $resource = ShareBeritaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
