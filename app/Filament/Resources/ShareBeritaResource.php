<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShareBeritaResource\Pages;
use App\Filament\Resources\ShareBeritaResource\RelationManagers;
use App\Models\ShareBerita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShareBeritaResource extends Resource
{
    protected static ?string $model = ShareBerita::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nama'),
                Tables\Columns\TextColumn::make('berita_title'),
                Tables\Columns\TextColumn::make('platform'),
                Tables\Columns\TextColumn::make('url_berita'),
                Tables\Columns\TextColumn::make('tanggal_share'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShareBeritas::route('/'),
            'create' => Pages\CreateShareBerita::route('/create'),
            'edit' => Pages\EditShareBerita::route('/{record}/edit'),
        ];
    }
}
