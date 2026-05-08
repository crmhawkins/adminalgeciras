<?php

namespace App\Filament\Resources\AbonoResource\Pages;

use App\Filament\Resources\AbonoResource;
use App\Mail\CodigoAccesoMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateAbono extends CreateRecord
{
    protected static string $resource = AbonoResource::class;

    protected function afterCreate(): void
    {
        $abono = $this->record;
        $email = $abono->usuario?->email ?? $abono->email ?? null;
        if ($email) {
            Mail::to($email)->send(new CodigoAccesoMail($abono));
        }
    }
}
