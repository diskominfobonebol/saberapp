<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShareBeritaResource\Pages;
use App\Filament\Resources\ShareBeritaResource\RelationManagers;
use App\Models\ShareBerita;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

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
                Tables\Columns\TextColumn::make('pegawai.nama')->searchable(),
                Tables\Columns\TextColumn::make('pegawai.opd.nama_opd'),
                Tables\Columns\TextColumn::make('berita_title'),
                Tables\Columns\TextColumn::make('platform'),
                Tables\Columns\TextColumn::make('url_berita'),
                Tables\Columns\TextColumn::make('tanggal_share'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pegawai.nama')
                    ->relationship('pegawai', 'nama'),

                Tables\Filters\SelectFilter::make('platform')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'twitter' => 'Twitter',
                    ]),

                // Filter Dinamis berdasarkan Tanggal Share
                Tables\Filters\SelectFilter::make('tanggal_share')
                    ->label('Tanggal Share')
                    ->options(function () {
                        // 1. Ambil semua tanggal unik dari database
                        return ShareBerita::query()
                            ->select(DB::raw('DATE(tanggal_share) as date')) // Ambil hanya tanggalnya
                            ->distinct()
                            ->orderBy('date', 'desc') // Urutkan dari terbaru
                            ->pluck('date', 'date') // Buat array [value => label]
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        // 2. Terapkan filter ke query tabel
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereDate('tanggal_share', $data['value']);
                    }),

                // Filter Dinamis berdasarkan Platform
                Tables\Filters\SelectFilter::make('platform')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'twitter' => 'Twitter',
                        'email' => 'Email',
                        'telegram' => 'Telegram',
                        'x' => 'X',
                    ]),

                // Filter Dinamis berdasarkan OPD
                Tables\Filters\SelectFilter::make('opd')
                    ->label('OPD')
                    ->relationship('pegawai.opd', 'nama_opd')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
