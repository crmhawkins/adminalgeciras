<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Pago;
use Carbon\Carbon;
use Livewire\Attributes\Computed;

class Reportes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title = 'Reportes de Ventas';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.reportes';

    public string $periodo = 'mes';
    public ?string $fechaDesde = null;
    public ?string $fechaHasta = null;

    public function mount(): void
    {
        $this->fechaDesde = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fechaHasta = Carbon::now()->format('Y-m-d');
    }

    #[Computed]
    public function stats(): array
    {
        $desde = $this->fechaDesde . ' 00:00:00';
        $hasta = $this->fechaHasta . ' 23:59:59';

        $data = \App\Models\Pago::where('estado', 'completado')
            ->whereBetween('createdAt', [$desde, $hasta])
            ->selectRaw('
                SUM(monto) as total,
                COUNT(*) as count,
                SUM(CASE WHEN tipo = "abono" THEN monto ELSE 0 END) as total_abonos,
                SUM(CASE WHEN tipo = "entrada" THEN monto ELSE 0 END) as total_entradas
            ')
            ->first();

        $total = (float) ($data->total ?? 0);
        $count = (int) ($data->count ?? 0);

        return [
            'total_recaudado'   => $total,
            'total_abonos'      => (float) ($data->total_abonos ?? 0),
            'total_entradas'    => (float) ($data->total_entradas ?? 0),
            'num_transacciones' => $count,
            'ticket_medio'      => $count > 0 ? ($total / $count) : 0,
        ];
    }

    #[Computed]
    public function porDia(): array
    {
        return Pago::where('estado', 'completado')
            ->whereBetween('createdAt', [
                $this->fechaDesde . ' 00:00:00',
                $this->fechaHasta . ' 23:59:59',
            ])
            ->selectRaw('DATE(createdAt) as fecha, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    public function setPeriodo(string $periodo): void
    {
        $this->periodo = $periodo;
        match ($periodo) {
            'semana' => [
                $this->fechaDesde,
                $this->fechaHasta,
            ] = [
                Carbon::now()->startOfWeek()->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ],
            'mes' => [
                $this->fechaDesde,
                $this->fechaHasta,
            ] = [
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ],
            'trimestre' => [
                $this->fechaDesde,
                $this->fechaHasta,
            ] = [
                Carbon::now()->subMonths(3)->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ],
            'año' => [
                $this->fechaDesde,
                $this->fechaHasta,
            ] = [
                Carbon::now()->startOfYear()->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ],
            default => null,
        };
    }

    public function exportarCSV()
    {
        $pagos = Pago::where('estado', 'completado')
            ->whereBetween('createdAt', [
                $this->fechaDesde . ' 00:00:00',
                $this->fechaHasta . ' 23:59:59',
            ])
            ->with('usuario')
            ->orderBy('createdAt', 'desc')
            ->get();

        return response()->streamDownload(
            function () use ($pagos) {
                $handle = fopen('php://output', 'w');
                fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
                fputcsv($handle, ['Fecha', 'Usuario', 'Email', 'Tipo', 'Monto', 'Estado']);
                foreach ($pagos as $p) {
                    $fecha = $p->createdAt instanceof \Carbon\Carbon
                        ? $p->createdAt->format('d/m/Y H:i')
                        : \Carbon\Carbon::parse($p->createdAt)->format('d/m/Y H:i');
                    fputcsv($handle, [
                        $fecha,
                        $p->usuario?->nombre ?? 'N/A',
                        $p->usuario?->email ?? 'N/A',
                        $p->tipo,
                        number_format($p->monto, 2),
                        $p->estado,
                    ]);
                }
                fclose($handle);
            },
            'ventas_' . $this->fechaDesde . '_' . $this->fechaHasta . '.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }
}
