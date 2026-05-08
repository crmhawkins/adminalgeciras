<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

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
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abono' => 'success',
                        'entrada' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completado' => 'success',
                        'pendiente' => 'warning',
                        'cancelado', 'expirado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('stripeSessionId')
                    ->label('Stripe Session')
                    ->limit(24)
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario.email')
                    ->label('Usuario Email')
                    ->searchable()
                    ->toggleable(),
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
                        'cancelado' => 'Cancelado',
                        'expirado' => 'Expirado',
                    ]),
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'abono'   => 'Abono',
                        'entrada' => 'Entrada',
                    ]),
                Filter::make('fecha_rango')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn ($q) => $q->whereDate('createdAt', '>=', $data['desde']))
                            ->when($data['hasta'], fn ($q) => $q->whereDate('createdAt', '<=', $data['hasta']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_csv_pagos')
                        ->label('Exportar CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csv = "ID,Usuario Email,Monto,Tipo,Estado,Fecha\n";
                            foreach ($records as $pago) {
                                $csv .= implode(',', [
                                    $pago->id,
                                    $pago->usuario?->email ?? '',
                                    $pago->monto ?? '',
                                    $pago->tipo ?? '',
                                    $pago->estado ?? '',
                                    $pago->createdAt ?? '',
                                ]) . "\n";
                            }
                            return response()->streamDownload(fn () => print($csv), 'pagos.csv', [
                                'Content-Type' => 'text/csv',
                            ]);
                        }),
                ]),
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
