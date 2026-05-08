<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Taquilla extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Taquilla';
    protected static ?string $title = 'Venta Presencial';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.taquilla';

    // Estado del formulario
    public int $paso = 1;

    // Paso 1 — Selección
    public ?int $partidoId = null;
    public ?int $asientoId = null;
    public string $tipoVenta = 'entrada'; // 'entrada' | 'abono'

    // Paso 2 — Disponibilidad (datos cargados del backend)
    public array $sectores = [];
    public string $infoPartido = '';

    // Paso 3 — Datos comprador
    public string $nombre = '';
    public string $apellidos = '';
    public string $email = '';
    public string $telefono = '';
    public string $dni = '';
    public string $metodoPago = 'efectivo';
    public ?float $precioManual = null;

    // Resultado
    public ?array $resultado = null;

    private function backendUrl(): string
    {
        return rtrim(config('services.backend.url', env('BACKEND_URL', 'https://backend-algeciras.hawkins.es')), '/');
    }

    private function backendAuth(): array
    {
        $user = config('services.backend.user');
        $pass = config('services.backend.pass');
        if (empty($user) || empty($pass)) {
            throw new \RuntimeException('Backend credentials not configured');
        }
        return [$user, $pass];
    }

    public function buscarDisponibilidad(): void
    {
        if (!$this->partidoId) {
            Notification::make()->title('Selecciona un partido')->warning()->send();
            return;
        }

        $url = $this->backendUrl() . "/api/interno/taquilla/disponibilidad/{$this->partidoId}";

        try {
            $response = Http::withBasicAuth(...$this->backendAuth())
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $this->sectores   = $data['sectores'] ?? [];
                $this->infoPartido = $data['partido'] ?? '';
                $this->paso = 2;
            } else {
                Notification::make()->title('Error: ' . ($response->json()['msg'] ?? 'Error backend'))->danger()->send();
            }
        } catch (\Exception $e) {
            Notification::make()->title('Error de conexión: ' . $e->getMessage())->danger()->send();
        }
    }

    public function seleccionarAsiento(int $asientoId): void
    {
        $this->asientoId = $asientoId;
        $this->paso = 3;
    }

    public function confirmarVenta(): void
    {
        if (empty($this->asientoId)) {
            Notification::make()
                ->title('Selecciona un asiento primero')
                ->warning()
                ->send();
            return;
        }

        if (!$this->nombre || !$this->metodoPago) {
            Notification::make()->title('Nombre y método de pago son obligatorios')->warning()->send();
            return;
        }

        if ($this->precioManual !== null) {
            $allowedEmails = config('admin.allowed_emails');
            if (empty($allowedEmails) || !in_array(auth()->user()->email, $allowedEmails)) {
                Notification::make()
                    ->title('Sin permisos para precio manual')
                    ->danger()
                    ->send();
                return;
            }
        }

        $url = $this->backendUrl() . '/api/interno/taquilla/' . $this->tipoVenta;

        $body = [
            'nombre'      => $this->nombre,
            'apellidos'   => $this->apellidos,
            'email'       => $this->email ?: null,
            'telefono'    => $this->telefono ?: null,
            'dni'         => $this->dni ?: null,
            'metodoPago'  => $this->metodoPago,
        ];

        if ($this->tipoVenta === 'entrada') {
            $body['partidoId']   = $this->partidoId;
            $body['asientoId']   = $this->asientoId;
            if ($this->precioManual !== null) $body['precioManual'] = $this->precioManual;
        } else {
            $body['asientoId']   = $this->asientoId;
            $body['precio']      = $this->precioManual ?? 100;
        }

        try {
            $response = Http::withBasicAuth(...$this->backendAuth())
                ->timeout(15)
                ->post($url, $body);

            $data = $response->json();

            if ($response->successful() && ($data['ok'] ?? false)) {
                $this->resultado = $data;
                $this->paso = 4;
                Notification::make()->title('Venta registrada correctamente')->success()->send();
            } else {
                $msg = $data['msg'] ?? 'Error desconocido';
                if ($response->status() === 409) {
                    Notification::make()->title('Asiento ya vendido o con abono activo')->danger()->send();
                } else {
                    Notification::make()->title("Error: {$msg}")->danger()->send();
                }
            }
        } catch (\Exception $e) {
            Notification::make()->title('Error de conexión: ' . $e->getMessage())->danger()->send();
        }
    }

    public function nuevaVenta(): void
    {
        $this->paso = 1;
        $this->partidoId = null;
        $this->asientoId = null;
        $this->sectores  = [];
        $this->nombre = $this->apellidos = $this->email = $this->telefono = $this->dni = '';
        $this->metodoPago = 'efectivo';
        $this->precioManual = null;
        $this->resultado = null;
    }
}
