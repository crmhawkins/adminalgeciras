<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/', fn() => redirect('/admin'));

Route::get('/tickets/{pagoId}', [TicketController::class, 'generarTicket'])
    ->middleware('auth')
    ->name('ticket.generar');
