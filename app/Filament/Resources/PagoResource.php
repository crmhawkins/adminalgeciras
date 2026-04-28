<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pagos';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Pagos';

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
                Tables\Columns\TextColumn::make('usuario.nombre')
                    ->label('Usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'completado',
                        'warning' => 'pendiente',
                        'danger' => 'fallido',
                        'secondary' => 'reembolsado',
                    ]),
                Tables\Columns\TextColumn::make('stripeSessionId')
                    ->label('Stripe Session')
                    ->limit(20)
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdAt')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('createdAt', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'completado' => 'Completado',
                        'pendiente' => 'Pendiente',
                        'fallido' => 'Fallido',
                        'reembolsado' => 'Reembolsado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
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
            'index' => Pages\ListPagos::route('/'),
        ];
    }
}
