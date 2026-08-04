<?php

namespace App\Filament\Resources\Diensten\Pages;

use App\Filament\Resources\Diensten\DienstResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiensten extends ListRecords
{
    protected static string $resource = DienstResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
