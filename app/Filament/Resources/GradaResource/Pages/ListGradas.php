<?php

namespace App\Filament\Resources\GradaResource\Pages;

use App\Filament\Resources\GradaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGradas extends ListRecords
{
    protected static string $resource = GradaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
