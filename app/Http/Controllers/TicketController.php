<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    public function generarTicket(int $pagoId)
    {
        $pago = Pago::with('usuario')->findOrFail($pagoId);

        // datosCompra es JSON con sector, asiento, partido, codigoAcceso, etc.
        $datos = is_array($pago->datosCompra)
            ? $pago->datosCompra
            : json_decode($pago->datosCompra ?? '{}', true) ?? [];

        $pdf = Pdf::loadView('tickets.entrada', [
            'pago'  => $pago,
            'datos' => $datos,
        ]);

        // Tamaño ticket térmico 80mm ≈ 226pt de ancho, altura variable
        $pdf->setPaper([0, 0, 226, 400], 'portrait');

        return $pdf->download("ticket-{$pagoId}.pdf");
    }
}
