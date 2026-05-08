<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartidoResource\Pages;
use App\Models\Partido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartidoResource extends Resource
{
    protected static ?string $model = Partido::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Partidos';

    protected static ?string $modelLabel = 'Partido';

    protected static ?string $pluralModelLabel = 'Partidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('equipoLocal')
                    ->label('Equipo Local')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('equipoVisitante')
                    ->label('Equipo Visitante')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required()
                    ->displayFormat('d/m/Y'),
                Forms\Components\TimePicker::make('hora')
                    ->label('Hora')
                    ->seconds(false)
                    ->required(),
                Forms\Components\TextInput::make('estadio')
                    ->label('Estadio')
                    ->maxLength(255)
                    ->default('Nuevo Mirador'),
                Forms\Components\TextInput::make('resultado')
                    ->label('Resultado')
                    ->placeholder('2-1')
                    ->maxLength(10),
                Forms\Components\TextInput::make('jornada')
                    ->label('Jornada')
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Toggle::make('activo')
                    ->label('Partido activo')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipoLocal')
                    ->label('Local')
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipoVisitante')
                    ->label('Visitante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora'),
                Tables\Columns\TextColumn::make('estadio')
                    ->label('Estadio'),
            ])
            ->defaultSort('fecha', 'desc')
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
            'index' => Pages\ListPartidos::route('/'),
            'create' => Pages\CreatePartido::route('/create'),
            'edit' => Pages\EditPartido::route('/{record}/edit'),
        ];
    }
}
