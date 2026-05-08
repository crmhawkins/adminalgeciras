<x-filament-panels::page>
    @if($this->paso === 1)
        {{-- PASO 1: Selección partido --}}
        <div class="space-y-4">
            <x-filament::section heading="Paso 1 — Selección">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipo de Venta</label>
                        <select wire:model="tipoVenta" class="w-full border rounded-lg px-3 py-2">
                            <option value="entrada">Entrada (partido específico)</option>
                            <option value="abono">Abono de temporada</option>
                        </select>
                    </div>
                    @if($tipoVenta === 'entrada')
                    <div>
                        <label class="block text-sm font-medium mb-1">ID Partido</label>
                        <input wire:model="partidoId" type="number" placeholder="Ej: 5"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>
                    @endif
                </div>
                <div class="mt-4">
                    <x-filament::button wire:click="buscarDisponibilidad" color="primary">
                        Ver disponibilidad →
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>

    @elseif($this->paso === 2)
        {{-- PASO 2: Mapa de asientos --}}
        <x-filament::section>
            <x-slot name="heading">
                Paso 2 — Asientos: {{ $this->infoPartido }}
            </x-slot>
            <div class="mb-3">
                <x-filament::button wire:click="$set('paso', 1)" color="gray" size="sm">
                    ← Volver
                </x-filament::button>
            </div>
            @foreach($this->sectores as $sector)
                <div class="mb-6">
                    <h3 class="font-bold text-lg mb-2">{{ $sector['nombre'] }}</h3>
                    @foreach($sector['filas'] as $fila)
                        <div class="flex flex-wrap gap-1 mb-1 items-center">
                            <span class="text-xs text-gray-500 w-10">F.{{ $fila['fila'] }}</span>
                            @foreach($fila['asientos'] as $asiento)
                                @if($asiento['estado'] === 'libre')
                                    <button wire:click="seleccionarAsiento({{ $asiento['id'] }})"
                                        title="Asiento {{ $asiento['numero'] }}"
                                        class="w-8 h-8 text-xs bg-green-100 hover:bg-green-500 hover:text-white border border-green-400 rounded text-green-800 font-bold transition-colors">
                                        {{ $asiento['numero'] }}
                                    </button>
                                @elseif($asiento['estado'] === 'abonado')
                                    <span title="Asiento {{ $asiento['numero'] }} — ABONADO"
                                        class="w-8 h-8 text-xs bg-blue-100 border border-blue-300 rounded text-blue-800 font-bold flex items-center justify-center">
                                        {{ $asiento['numero'] }}
                                    </span>
                                @else
                                    <span title="Asiento {{ $asiento['numero'] }} — OCUPADO"
                                        class="w-8 h-8 text-xs bg-red-100 border border-red-300 rounded text-red-400 flex items-center justify-center line-through">
                                        {{ $asiento['numero'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
            <div class="flex gap-4 text-sm mt-4">
                <span class="flex items-center gap-1"><span class="w-4 h-4 bg-green-100 border border-green-400 rounded inline-block"></span> Libre</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 bg-red-100 border border-red-300 rounded inline-block"></span> Ocupado</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 bg-blue-100 border border-blue-300 rounded inline-block"></span> Abonado</span>
            </div>
        </x-filament::section>

    @elseif($this->paso === 3)
        {{-- PASO 3: Datos comprador --}}
        <x-filament::section heading="Paso 3 — Datos del comprador">
            <div class="mb-3">
                <x-filament::button wire:click="$set('paso', 2)" color="gray" size="sm">
                    ← Volver
                </x-filament::button>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input wire:model="nombre" type="text" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apellidos</label>
                    <input wire:model="apellidos" type="text" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input wire:model="email" type="email" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input wire:model="telefono" type="tel" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">DNI</label>
                    <input wire:model="dni" type="text" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Método de Pago *</label>
                    <select wire:model="metodoPago" class="w-full border rounded-lg px-3 py-2">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Precio Manual (€) — dejar vacío para precio del sector</label>
                    <input wire:model="precioManual" type="number" step="0.01" class="w-full border rounded-lg px-3 py-2" placeholder="Precio del sector por defecto">
                </div>
            </div>
            <div class="mt-6">
                <x-filament::button wire:click="confirmarVenta" color="success" size="lg">
                    ✅ Confirmar Venta
                </x-filament::button>
            </div>
        </x-filament::section>

    @elseif($this->paso === 4)
        {{-- PASO 4: Confirmación --}}
        <x-filament::section>
            <div class="text-center py-8">
                <div class="text-6xl mb-4">✅</div>
                <h2 class="text-2xl font-bold text-green-600 mb-2">Venta Registrada</h2>
                @if($this->resultado)
                    <div class="bg-gray-50 rounded-xl p-6 text-left max-w-md mx-auto mt-4 space-y-2">
                        @if(isset($resultado['partido']))
                            <p><strong>Partido:</strong> {{ $resultado['partido'] }}</p>
                        @endif
                        @if(isset($resultado['asientoInfo']))
                            <p><strong>Asiento:</strong> {{ $resultado['asientoInfo'] }}</p>
                        @endif
                        @if(isset($resultado['precio']))
                            <p><strong>Precio:</strong> {{ number_format($resultado['precio'], 2) }} €</p>
                        @endif
                        @if(isset($resultado['codigoAcceso']))
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-500 mb-1">CÓDIGO DE ACCESO</p>
                                <span class="text-3xl font-bold tracking-widest text-red-600">{{ $resultado['codigoAcceso'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="mt-6 flex justify-center gap-3 flex-wrap">
                    @if(isset($resultado['pagoId']))
                    <a href="{{ route('ticket.generar', $resultado['pagoId']) }}" target="_blank">
                        <x-filament::button color="warning">
                            🖨️ Imprimir Ticket
                        </x-filament::button>
                    </a>
                    @endif
                    <x-filament::button wire:click="nuevaVenta" color="primary">
                        + Nueva Venta
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
