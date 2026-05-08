<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    text-align: center;
    color: #111;
    width: 100%;
  }
  .logo {
    font-size: 17px;
    font-weight: bold;
    color: #C8102E;
    letter-spacing: 1px;
    margin-bottom: 2px;
  }
  .sublogo {
    font-size: 9px;
    color: #555;
    margin-bottom: 4px;
  }
  .divider {
    border: none;
    border-top: 1px dashed #555;
    margin: 6px 0;
  }
  .tipo {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 4px 0;
  }
  .field {
    display: flex;
    justify-content: space-between;
    margin: 3px 6px;
    font-size: 10px;
  }
  .field span:first-child { color: #555; }
  .field span:last-child  { font-weight: bold; text-align: right; max-width: 60%; }
  .codigo-label {
    font-size: 9px;
    color: #777;
    margin-top: 6px;
    margin-bottom: 2px;
  }
  .codigo {
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 4px;
    color: #C8102E;
  }
  .footer {
    font-size: 8px;
    color: #888;
    margin-top: 6px;
  }
</style>
</head>
<body>

  <div class="logo">ALGECIRAS CF</div>
  <div class="sublogo">Estadio Nuevo Mirador</div>

  <hr class="divider">

  <div class="tipo">
    {{ strtoupper($pago->tipo === 'abono' ? 'Abono Temporada' : 'Entrada') }}
  </div>

  <hr class="divider">

  @php
    $titular  = $pago->usuario?->nombre ?? ($datos['nombre'] ?? 'N/A');
    $apellidos = $pago->usuario?->apellidos ?? ($datos['apellidos'] ?? '');
    $sector   = $datos['sector'] ?? ($datos['sectorNombre'] ?? 'N/A');
    $asiento  = $datos['asiento'] ?? ($datos['asientoInfo'] ?? ($pago->asientoId ?? 'N/A'));
    $partido  = $datos['partido'] ?? ($datos['infoPartido'] ?? null);
    $codigo   = $datos['codigoAcceso'] ?? null;
    $precio   = $pago->monto ?? ($datos['precio'] ?? null);
    $fecha    = $pago->{$pago::CREATED_AT} ?? now();
    $fechaFmt = $fecha instanceof \Carbon\Carbon ? $fecha->format('d/m/Y H:i') : \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i');
  @endphp

  <div class="field"><span>Titular:</span><span>{{ trim($titular . ' ' . $apellidos) }}</span></div>

  @if($partido)
  <div class="field"><span>Partido:</span><span>{{ $partido }}</span></div>
  @endif

  <div class="field"><span>Sector:</span><span>{{ $sector }}</span></div>
  <div class="field"><span>Asiento:</span><span>{{ $asiento }}</span></div>

  @if($precio !== null)
  <div class="field"><span>Precio:</span><span>{{ number_format((float)$precio, 2) }} €</span></div>
  @endif

  <div class="field"><span>Fecha:</span><span>{{ $fechaFmt }}</span></div>
  <div class="field"><span>Pago #:</span><span>{{ $pago->id }}</span></div>

  <hr class="divider">

  @if($codigo)
  <div class="codigo-label">CÓDIGO DE ACCESO</div>
  <div class="codigo">{{ $codigo }}</div>
  <hr class="divider">
  @endif

  <div class="footer">Estadio Nuevo Mirador · Algeciras, Cádiz</div>
  <div class="footer">Conserva este ticket hasta salir del estadio</div>

</body>
</html>
