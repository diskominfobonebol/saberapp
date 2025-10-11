<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpdResource\Pages;
use App\Filament\Resources\OpdResource\RelationManagers;
use App\Models\Opd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpdResource extends Resource
{
    protected static ?string $model = Opd::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_opd')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id') // Target kolom di tabel Anda
                    ->relationship('user', 'name') // 'user' adalah nama relasi, 'name' adalah kolom yang ditampilkan
                    ->searchable() // Opsional: untuk bisa mencari nama user
                    ->preload()    // Opsional: untuk memuat opsi saat halaman dibuka
                    ->required()
                    ->label('Nama User'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_opd')->searchable(),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('user.name'),
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
            'index' => Pages\ListOpds::route('/'),
            'create' => Pages\CreateOpd::route('/create'),
            'edit' => Pages\EditOpd::route('/{record}/edit'),
        ];
    }
}
