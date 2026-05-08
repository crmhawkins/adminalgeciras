<?php

namespace App\Filament\Widgets;

use App\Models\Abono;
use App\Models\Asiento;
use App\Models\Pago;
use App\Models\Partido;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAbonados = Abono::where('activo', true)->count();

        $ocupados = Asiento::where('estado', 'ocupado')->count();
        $disponibles = Asiento::where('estado', 'disponible')->count();

        $ingresosMes = Pago::where('estado', 'completado')
            ->whereYear('createdAt', now()->year)
            ->whereMonth('createdAt', now()->month)
            ->sum('monto');

        $nuevosEstaSemana = Abono::whereBetween('createdAt', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();

        $proximoPartido = Partido::where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->first();

        $proximoTexto = $proximoPartido
            ? $proximoPartido->equipoLocal . ' vs ' . $proximoPartido->equipoVisitante
            : 'Sin partidos próximos';
        $proximoFecha = $proximoPartido
            ? Carbon::parse($proximoPartido->fecha)->format('d/m/Y') . ' ' . $proximoPartido->hora
            : '';

        return [
            Stat::make('Abonados Activos', $totalAbonados)
                ->description('Abonos activos en vigor')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Ingresos Este Mes', '€' . number_format($ingresosMes, 2, ',', '.'))
                ->description('Pagos completados ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Nuevos Esta Semana', $nuevosEstaSemana)
                ->description('Abonos creados esta semana')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Próximo Partido', $proximoTexto)
                ->description($proximoFecha)
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),

            Stat::make('Asientos Ocupados', $ocupados . ' / ' . ($ocupados + $disponibles))
                ->description($disponibles . ' disponibles')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('warning'),
        ];
    }
}
