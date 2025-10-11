<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('opd_id')
                    ->relationship('opd', 'nama_opd')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Nama OPD'),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('gelar_depan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('gelar_belakang')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\Select::make('agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen' => 'Kristen',
                        'Katolik' => 'Katolik',
                        'Hindu' => 'Hindu',
                        'Budha' => 'Budha',
                        'Konghucu' => 'Konghucu',
                    ])
                    ->required(),
                Forms\Components\Select::make('status_perkawinan')
                    ->options([
                        'Belum Kawin' => 'Belum Kawin',
                        'Kawin' => 'Kawin',
                        'Cerai' => 'Cerai',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('nomor_hp')
                    ->maxLength(255),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_pegawai')
                    ->options([
                        'Pegawai Negeri Sipil' => 'Pegawai Negeri Sipil',
                        'Pegawai Negeri Non Sipil' => 'Pegawai Negeri Non Sipil',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('golongan')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tmt_golongan')
                    ->required(),
                Forms\Components\Select::make('jenis_jabatan')
                    ->options([
                        'PNS' => 'PNS',
                        'TNI' => 'TNI',
                        'POLRI' => 'POLRI',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jabatan')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tmt_jabatan')
                    ->required(),
                Forms\Components\TextInput::make('pendidikan_terakhir')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tahun_lulus')
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('nip')->searchable(),
                Tables\Columns\TextColumn::make('opd.nama_opd'),
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
