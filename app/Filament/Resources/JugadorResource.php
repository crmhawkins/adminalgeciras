<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JugadorResource\Pages;
use App\Models\Jugador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JugadorResource extends Resource
{
    protected static ?string $model = Jugador::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Jugadores';

    protected static ?string $modelLabel = 'Jugador';

    protected static ?string $pluralModelLabel = 'Jugadores';

    protected static ?string $navigationGroup = 'Equipo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre completo')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nombreCorto')
                    ->label('Nombre corto')
                    ->maxLength(100),

                Forms\Components\TextInput::make('sofascoreId')
                    ->label('SofaScore ID')
                    ->numeric()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('dorsal')
                    ->label('Dorsal')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99),

                Forms\Components\Select::make('posicion')
                    ->label('Posición')
                    ->options([
                        'G' => 'Portero',
                        'D' => 'Defensa',
                        'M' => 'Centrocampista',
                        'F' => 'Delantero',
                    ]),

                Forms\Components\TextInput::make('foto')
                    ->label('URL foto')
                    ->url()
                    ->maxLength(500),

                Forms\Components\TextInput::make('edad')
                    ->label('Edad')
                    ->numeric()
                    ->minValue(15)
                    ->maxValue(50),

                Forms\Components\TextInput::make('nacionalidad')
                    ->label('Nacionalidad')
                    ->maxLength(100),

                Forms\Components\TextInput::make('altura')
                    ->label('Altura (cm)')
                    ->numeric()
                    ->minValue(150)
                    ->maxValue(220),

                Forms\Components\Select::make('piePref')
                    ->label('Pie preferido')
                    ->options([
                        'left'   => 'Izquierdo',
                        'right'  => 'Derecho',
                        'both'   => 'Ambidiestro',
                    ]),

                Forms\Components\TextInput::make('valorMercado')
                    ->label('Valor de mercado (€)')
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dorsal')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->width(40)
                    ->height(40),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('posicion')
                    ->label('Pos.')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'G' => 'warning',
                        'D' => 'success',
                        'M' => 'info',
                        'F' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'G' => 'POR',
                        'D' => 'DEF',
                        'M' => 'CEN',
                        'F' => 'DEL',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('nacionalidad')
                    ->label('País')
                    ->searchable(),

                Tables\Columns\TextColumn::make('edad')
                    ->label('Edad')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sofascoreId')
                    ->label('SofaScore ID')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('posicion')
            ->filters([
                Tables\Filters\SelectFilter::make('posicion')
                    ->label('Posición')
                    ->options([
                        'G' => 'Porteros',
                        'D' => 'Defensas',
                        'M' => 'Centrocampistas',
                        'F' => 'Delanteros',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListJugadores::route('/'),
            'create' => Pages\CreateJugador::route('/create'),
            'edit'   => Pages\EditJugador::route('/{record}/edit'),
        ];
    }
}
