<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbonoResource\Pages;
use App\Models\Abono;
use App\Models\Sector;
use App\Models\Usuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbonoResource extends Resource
{
    protected static ?string $model = Abono::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Abonos';

    protected static ?string $modelLabel = 'Abono';

    protected static ?string $pluralModelLabel = 'Abonos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuarioId')
                    ->label('Usuario')
                    ->relationship('usuario', 'nombre')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('sectorId')
                    ->label('Sector')
                    ->relationship('sector', 'nombre')
                    ->required(),
                Forms\Components\Select::make('asientoId')
                    ->label('Asiento')
                    ->relationship('asiento', 'numero')
                    ->searchable(),
                Forms\Components\TextInput::make('temporada')
                    ->label('Temporada')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'pendiente' => 'Pendiente',
                        'cancelado' => 'Cancelado',
                        'expirado' => 'Expirado',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.nombre')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sector.nombre')
                    ->label('Sector')
                    ->sortable(),
                Tables\Columns\TextColumn::make('asiento')
                    ->label('Asiento')
                    ->getStateUsing(fn ($record) => $record->asiento ? "Fila {$record->asiento->fila} - Nº {$record->asiento->numero}" : '-'),
                Tables\Columns\TextColumn::make('temporada')
                    ->label('Temporada')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'pendiente',
                        'danger' => 'cancelado',
                        'secondary' => 'expirado',
                    ]),
                Tables\Columns\TextColumn::make('createdAt')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'pendiente' => 'Pendiente',
                        'cancelado' => 'Cancelado',
                        'expirado' => 'Expirado',
                    ]),
                Tables\Filters\SelectFilter::make('temporada')
                    ->label('Temporada')
                    ->options(fn () => Abono::query()->distinct()->pluck('temporada', 'temporada')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbonos::route('/'),
            'create' => Pages\CreateAbono::route('/create'),
            'edit' => Pages\EditAbono::route('/{record}/edit'),
        ];
    }
}
