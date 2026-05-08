<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pago;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class AlertarPagosPendientes extends Command
{
    protected $signature = 'pagos:alertar-pendientes';
    protected $description = 'Notifica al admin sobre pagos pendientes más de 24h';

    public function handle(): int
    {
        $pendientes = Pago::where('estado', 'pendiente')
            ->where('createdAt', '<', Carbon::now()->subHours(24))
            ->count();

        if ($pendientes > 0) {
            $admins = User::all();

            foreach ($admins as $admin) {
                Notification::make()
                    ->title("{$pendientes} pagos pendientes >24h")
                    ->body('Revisar en la sección de Pagos')
                    ->warning()
                    ->sendToDatabase($admin);
            }

            $this->info("Notificación enviada: {$pendientes} pagos pendientes encontrados.");
        } else {
            $this->info('Sin pagos pendientes >24h.');
        }

        return self::SUCCESS;
    }
}
