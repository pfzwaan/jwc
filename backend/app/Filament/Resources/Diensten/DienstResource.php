<?php

namespace App\Filament\Resources\Diensten;

use App\Filament\Resources\Diensten\Pages\CreateDienst;
use App\Filament\Resources\Diensten\Pages\EditDienst;
use App\Filament\Resources\Diensten\Pages\ListDiensten;
use App\Filament\Resources\Diensten\Schemas\DienstForm;
use App\Filament\Resources\Diensten\Tables\DienstenTable;
use App\Models\Dienst;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/** CPT: Diensten — its own entry in the admin navigation, separate from Blog. */
class DienstResource extends Resource
{
    protected static ?string $model = Dienst::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Diensten';

    protected static ?string $modelLabel = 'dienst';

    protected static ?string $pluralModelLabel = 'diensten';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DienstForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DienstenTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiensten::route('/'),
            'create' => CreateDienst::route('/create'),
            'edit' => EditDienst::route('/{record}/edit'),
        ];
    }
}
