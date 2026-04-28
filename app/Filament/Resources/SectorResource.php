<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectorResource\Pages;
use App\Models\Sector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SectorResource extends Resource
{
    protected static ?string $model = Sector::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Sectores';

    protected static ?string $modelLabel = 'Sector';

    protected static ?string $pluralModelLabel = 'Sectores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gradaId')
                    ->label('Grada')
                    ->relationship('grada', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('capacidad')
                    ->label('Capacidad')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('precio')
                    ->label('Precio (€)')
                    ->numeric()
                    ->step(0.01)
                    ->required(),
                Forms\Components\TextInput::make('imagen')
                    ->label('Imagen (URL)')
                    ->maxLength(500),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grada.nombre')
                    ->label('Grada')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacidad')
                    ->label('Capacidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSectors::route('/'),
            'create' => Pages\CreateSector::route('/create'),
            'edit' => Pages\EditSector::route('/{record}/edit'),
        ];
    }
}
