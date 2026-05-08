<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradaResource\Pages;
use App\Models\Grada;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GradaResource extends Resource
{
    protected static ?string $model = Grada::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Gradas';

    protected static ?string $modelLabel = 'Grada';

    protected static ?string $pluralModelLabel = 'Gradas';

    protected static ?string $navigationGroup = 'Estadio';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('imagen')
                    ->label('Imagen (URL)')
                    ->url()
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(60)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sectores_count')
                    ->label('Sectores')
                    ->counts('sectores')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('createdAt')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('nombre')
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
            'index' => Pages\ListGradas::route('/'),
            'create' => Pages\CreateGrada::route('/create'),
            'edit' => Pages\EditGrada::route('/{record}/edit'),
        ];
    }
}
