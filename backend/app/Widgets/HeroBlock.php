<?php

namespace App\Widgets;

use App\Filament\Forms\Components\WebImage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/**
 * Section 3 — the full-bleed hero (Figma 31761:4121). Background image or video
 * with a dark overlay, centred title + intro and up to two buttons.
 */
class HeroBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Hero';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedSparkles;
    }

    public function form(): array
    {
        return [
            TextInput::make('title')->label('Titel (H1)')->columnSpanFull()
                ->helperText('Zet *sterretjes* om een deel in de accentkleur te tonen, bv. Wij zijn *Johan Willemsen* Computers.'),
            Textarea::make('body')->label('Introtekst')->rows(3)->columnSpanFull(),

            Select::make('size')
                ->label('Hoogte')
                ->options(['full' => 'Volledig (homepage)', 'compact' => 'Compact (subpagina)'])
                ->default('full')
                ->selectablePlaceholder(false),

            Select::make('align')
                ->label('Uitlijning')
                ->options(['center' => 'Gecentreerd', 'left' => 'Links'])
                ->default('center')
                ->selectablePlaceholder(false),

            Select::make('pattern')
                ->label('Achtergrondpatroon')
                ->options(['left' => 'Links', 'right' => 'Rechts', 'none' => 'Geen'])
                ->default('left')
                ->selectablePlaceholder(false),

            WebImage::make('image')
                ->label('Achtergrondafbeelding')
                ->directory('hero')->disk('public')->visibility('public')
                ->helperText('Leeg = de standaard achtergrond uit het ontwerp.'),

            // The Figma hero is a video placeholder; a video wins over the image.
            FileUpload::make('video')
                ->label('Achtergrondvideo')
                ->acceptedFileTypes(['video/mp4', 'video/webm'])
                // Deliberately no maxSize(): its `max` rule is the only thing that
                // calls TemporaryUploadedFile::getSize(), which threw
                // UnableToRetrieveMetadata on the temporary file and failed every
                // save. upload_max_filesize (128M) in the Dockerfile is the real
                // ceiling, and it rejects oversized files before this point.
                ->directory('hero')->disk('public')->visibility('public')
                ->helperText('Optioneel, max 128 MB. Een .mp4 of .webm die automatisch en zonder geluid afspeelt. Een video gaat vóór de achtergrondafbeelding. Houd de bestandsnaam kort — namen boven ± 150 tekens kan de server niet wegschrijven.'),

            Select::make('video_speed')
                ->label('Afspeelsnelheid video')
                ->options([
                    '0.25' => 'Kwart snelheid (0,25×)',
                    '0.5' => 'Half zo snel (0,5×)',
                    '0.75' => 'Iets langzamer (0,75×)',
                    '1' => 'Normaal (1×)',
                ])
                ->default('1')
                ->selectablePlaceholder(false)
                ->helperText('Alleen van toepassing als er een video is ingesteld.'),

            Toggle::make('show_overlay')->label('Donkere overlay tonen')->default(true),

            Repeater::make('buttons')
                ->label('Knoppen')
                ->schema([
                    TextInput::make('label')->label('Knoptekst')->required(),
                    // Plain TextInput — anchors, relative paths and full URLs must all save.
                    TextInput::make('href')->label('Link')->placeholder('/diensten')->required(),
                    Select::make('variant')
                        ->label('Stijl')
                        ->options(['primary' => 'Oranje', 'outline' => 'Wit met rand'])
                        ->default('primary')
                        ->selectablePlaceholder(false),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->maxItems(3)
                ->addActionLabel('Knop toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return [
            'title' => '',
            'body' => '',
            'size' => 'full',
            'align' => 'center',
            'pattern' => 'left',
            'image' => '',
            'video' => '',
            'video_speed' => '1',
            'show_overlay' => true,
            'buttons' => [],
        ];
    }
}
