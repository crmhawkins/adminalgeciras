<?php

namespace App\Filament\Resources\GradaResource\Pages;

use App\Filament\Resources\GradaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrada extends EditRecord
{
    protected static string $resource = GradaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
