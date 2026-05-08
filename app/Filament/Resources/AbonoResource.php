<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbonoResource\Pages;
use App\Mail\CodigoAccesoMail;
use App\Models\Abono;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class AbonoResource extends Resource
{
    protected static ?string $model = Abono::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Abonos';

    protected static ?string $modelLabel = 'Abono';

    protected static ?string $pluralModelLabel = 'Abonos';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['sector', 'usuario']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigoAbonado')
                    ->label('Nº Abonado')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('apellidos')
                    ->label('Apellidos')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email(),
                Forms\Components\TextInput::make('codigoAcceso')
                    ->label('Código de Acceso')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono'),
                Forms\Components\TextInput::make('dni')
                    ->label('DNI'),
                Forms\Components\DatePicker::make('fechaNacimiento')
                    ->label('Fecha de nacimiento'),
                Forms\Components\Select::make('genero')
                    ->label('Género')
                    ->options(['masculino' => 'Masculino', 'femenino' => 'Femenino']),
                Forms\Components\TextInput::make('precio')
                    ->label('Precio (€)')
                    ->numeric(),
                Forms\Components\DatePicker::make('fechaInicio')
                    ->label('Inicio'),
                Forms\Components\DatePicker::make('fechaFin')
                    ->label('Fin'),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('codigoAbonado')
            ->columns([
                Tables\Columns\TextColumn::make('codigoAbonado')
                    ->label('Nº')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos')
                    ->label('Apellidos')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('codigoAcceso')
                    ->label('Código Acceso')
                    ->copyable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('fechaInicio')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('fechaFin')
                    ->label('Hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sector.nombre')
                    ->label('Sector')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('usuario.email')
                    ->label('Email Usuario')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Exportar CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            $abonos = \App\Models\Abono::whereIn('id', $ids)->with(['usuario', 'sector'])->get();
                            return response()->streamDownload(function () use ($abonos) {
                                $handle = fopen('php://output', 'w');
                                fputs($handle, "\xEF\xBB\xBF");
                                fputcsv($handle, ['ID', 'Titular', 'Email', 'DNI', 'Sector', 'Asiento', 'Estado', 'Fecha Inicio', 'Fecha Fin']);
                                foreach ($abonos as $abono) {
                                    fputcsv($handle, [
                                        $abono->id,
                                        $abono->nombre . ' ' . $abono->apellidos,
                                        $abono->usuario?->email ?? '',
                                        $abono->dni ?? '',
                                        $abono->sector?->nombre ?? '',
                                        $abono->asientoId ?? '',
                                        $abono->activo ? 'Activo' : 'Inactivo',
                                        $abono->fechaInicio ?? '',
                                        $abono->fechaFin ?? '',
                                    ]);
                                }
                                fclose($handle);
                            }, 'abonos_export.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
                        }),
                    Tables\Actions\BulkAction::make('renovar')
                        ->label('Renovar (+1 año)')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($abono) {
                                $abono->update([
                                    'fechaFin' => Carbon::parse($abono->fechaFin)->addYear(),
                                    'activo' => true,
                                ]);
                            });
                        }),
                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['activo' => false])),
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
