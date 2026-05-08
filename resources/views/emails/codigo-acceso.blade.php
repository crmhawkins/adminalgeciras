<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu código de acceso — Algeciras CF</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #C8102E; color: #fff; padding: 32px 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 32px 24px; }
        .code-box { background: #f8f8f8; border: 2px dashed #C8102E; border-radius: 8px; text-align: center; padding: 24px; margin: 24px 0; }
        .code-box .code { font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #C8102E; font-family: monospace; }
        .details { background: #fafafa; border-radius: 6px; padding: 16px; margin-top: 16px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 8px 4px; font-size: 14px; color: #444; }
        .details td:first-child { font-weight: bold; color: #222; width: 40%; }
        .footer { background: #f0f0f0; text-align: center; padding: 16px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Algeciras CF — Abono de Temporada</h1>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $abono->usuario?->nombre ?? $abono->nombre ?? 'Abonado' }}</strong>,</p>
            <p>Tu abono ha sido registrado correctamente. Aquí tienes tu código de acceso al estadio:</p>

            <div class="code-box">
                <div style="font-size:13px; color:#666; margin-bottom:8px;">CÓDIGO DE ACCESO</div>
                <div class="code">{{ $abono->codigoAcceso }}</div>
            </div>

            <div class="details">
                <table>
                    <tr>
                        <td>Sector:</td>
                        <td>{{ $abono->sector?->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Asiento:</td>
                        <td>{{ $abono->asientoId ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Temporada:</td>
                        <td>
                            @if($abono->fechaInicio && $abono->fechaFin)
                                {{ \Carbon\Carbon::parse($abono->fechaInicio)->format('d/m/Y') }}
                                –
                                {{ \Carbon\Carbon::parse($abono->fechaFin)->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Precio:</td>
                        <td>{{ $abono->precio ? number_format($abono->precio, 2, ',', '.') . ' €' : '—' }}</td>
                    </tr>
                </table>
            </div>

            <p style="margin-top:24px; font-size:13px; color:#666;">
                Presenta este código en el acceso al estadio. Si tienes cualquier duda, contacta con el club.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Algeciras CF. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
