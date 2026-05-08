<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsientoResource\Pages;
use App\Models\Asiento;
use App\Models\Sector;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AsientoResource extends Resource
{
    protected static ?string $model = Asiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Asientos';

    protected static ?string $modelLabel = 'Asiento';

    protected static ?string $pluralModelLabel = 'Asientos';

    protected static ?string $navigationGroup = 'Estadio';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sector.nombre')
                    ->label('Sector')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fila')
                    ->label('Fila')
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero')
                    ->label('Número')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disponible' => 'success',
                        'ocupado'    => 'danger',
                        'reservado'  => 'warning',
                        default      => 'gray',
                    }),
            ])
            ->defaultSort('id')
            ->filters([
                Tables\Filters\SelectFilter::make('sectorId')
                    ->label('Sector')
                    ->relationship('sector', 'nombre'),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible' => 'Disponible',
                        'ocupado'    => 'Ocupado',
                        'reservado'  => 'Reservado',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsientos::route('/'),
        ];
    }
}
