<?php

namespace App\Filament\Widgets;

use App\Models\Abono;
use App\Models\Asiento;
use App\Models\Pago;
use App\Models\Partido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAbonados = Abono::where('estado', 'activo')->count();
        $ocupados = Asiento::where('estado', 'ocupado')->count();
        $disponibles = Asiento::where('estado', 'disponible')->count();
        $ingresos = (float) Pago::where('estado', 'completado')->sum('monto');
        $proximoPartido = Partido::where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->first();

        $proximoTexto = $proximoPartido
            ? $proximoPartido->equipoLocal . ' vs ' . $proximoPartido->equipoVisitante
            : 'Sin partidos próximos';
        $proximoFecha = $proximoPartido
            ? \Carbon\Carbon::parse($proximoPartido->fecha)->format('d/m/Y') . ' ' . $proximoPartido->hora
            : '';

        return [
            Stat::make('Total Abonados', $totalAbonados)
                ->description('Abonos activos')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Asientos Ocupados', $ocupados . ' / ' . ($ocupados + $disponibles))
                ->description($disponibles . ' disponibles')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('warning'),

            Stat::make('Ingresos Totales', '€ ' . number_format($ingresos, 2, ',', '.'))
                ->description('Pagos completados')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Próximo Partido', $proximoTexto)
                ->description($proximoFecha)
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),
        ];
    }
}
