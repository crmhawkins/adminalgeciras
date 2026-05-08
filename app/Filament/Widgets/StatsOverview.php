<?php

namespace App\Filament\Widgets;

use App\Models\Abono;
use App\Models\Asiento;
use App\Models\Partido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAbonados = Abono::where('activo', true)->count();
        // Conteo global histórico — no filtrado por partido activo
        $ocupados = Asiento::where('estado', 'ocupado')->count();
        $disponibles = Asiento::where('estado', 'disponible')->count();
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

            Stat::make('Próximo Partido', $proximoTexto)
                ->description($proximoFecha)
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),
        ];
    }
}
