<x-filament-panels::page>
    {{-- Filtros de período --}}
    <x-filament::section>
        <div class="flex flex-wrap items-end gap-4">
            {{-- Botones período rápido --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Período</label>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['semana' => 'Semana', 'mes' => 'Mes', 'trimestre' => 'Trimestre', 'año' => 'Año'] as $key => $label)
                        <button
                            wire:click="setPeriodo('{{ $key }}')"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                                {{ $this->periodo === $key
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Datepicker desde --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Desde</label>
                <input
                    type="date"
                    wire:model.live="fechaDesde"
                    class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm
                           bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            {{-- Datepicker hasta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasta</label>
                <input
                    type="date"
                    wire:model.live="fechaHasta"
                    class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm
                           bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            {{-- Export CSV --}}
            <div class="ml-auto">
                <x-filament::button
                    wire:click="exportarCSV"
                    color="gray"
                    icon="heroicon-o-arrow-down-tray">
                    Exportar CSV
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    {{-- Cards de estadísticas --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <x-filament::card>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total recaudado</div>
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                {{ number_format($this->stats['total_recaudado'], 2) }}€
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-500 dark:text-gray-400">Abonos</div>
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                {{ number_format($this->stats['total_abonos'], 2) }}€
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-500 dark:text-gray-400">Entradas</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">
                {{ number_format($this->stats['total_entradas'], 2) }}€
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-500 dark:text-gray-400">Transacciones</div>
            <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                {{ number_format($this->stats['num_transacciones']) }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-500 dark:text-gray-400">Ticket medio</div>
            <div class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                {{ number_format($this->stats['ticket_medio'], 2) }}€
            </div>
        </x-filament::card>
    </div>

    {{-- Tabla por día --}}
    <x-filament::section heading="Ventas por día">
        @php $porDia = $this->porDia @endphp

        @if(count($porDia) === 0)
            <p class="text-gray-500 dark:text-gray-400 text-sm py-4 text-center">
                Sin datos para el período seleccionado.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 font-semibold text-gray-700 dark:text-gray-300">Fecha</th>
                            <th class="text-right py-2 px-3 font-semibold text-gray-700 dark:text-gray-300">Transacciones</th>
                            <th class="text-right py-2 px-3 font-semibold text-gray-700 dark:text-gray-300">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; $grandCount = 0; @endphp
                        @foreach($porDia as $fila)
                            @php
                                $grandTotal += $fila['total'];
                                $grandCount += $fila['cantidad'];
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-2 px-3 text-gray-800 dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}
                                </td>
                                <td class="py-2 px-3 text-right text-gray-600 dark:text-gray-400">
                                    {{ $fila['cantidad'] }}
                                </td>
                                <td class="py-2 px-3 text-right font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($fila['total'], 2) }}€
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/40">
                            <td class="py-2 px-3 font-bold text-gray-800 dark:text-gray-200">Total</td>
                            <td class="py-2 px-3 text-right font-bold text-gray-800 dark:text-gray-200">{{ $grandCount }}</td>
                            <td class="py-2 px-3 text-right font-bold text-primary-600 dark:text-primary-400">{{ number_format($grandTotal, 2) }}€</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
