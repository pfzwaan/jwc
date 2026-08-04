<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Site-wide settings — logo, tagline, contact details and socials. Feeds the
 * header, the footer's logo/tagline and Contact columns, and the contact form.
 * A single record, key/value backed by the Setting model.
 */
class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $title = 'Instellingen';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::values());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bedrijfsgegevens')->schema([
                TextInput::make('company_name')->label('Bedrijfsnaam')->placeholder('Johan Willemsen Computers'),
                Textarea::make('tagline')->label('Tagline')->rows(2)->columnSpanFull(),
                FileUpload::make('logo')->label('Logo')->image()
                    ->directory('settings')->disk('public')->visibility('public'),
                FileUpload::make('logo_white')->label('Logo (wit, voor donkere achtergrond)')->image()
                    ->directory('settings')->disk('public')->visibility('public'),
            ])->columns(2),

            Section::make('Contact')->schema([
                TextInput::make('footer_contact_heading')->label('Kop (footer)')->placeholder('Contact'),
                TextInput::make('contact_email')->label('E-mailadres')->placeholder('info@johanwillemsen.nl'),
                TextInput::make('contact_phone')->label('Telefoonnummer')->placeholder('0488-712003'),
                TextInput::make('contact_phone_hours')->label('Bereikbaarheid')->placeholder('9u t/m 21:30u'),
                TextInput::make('contact_fax')->label('Fax')->placeholder('0847-102831'),
                TextInput::make('contact_address_line1')->label('Adres — regel 1')->placeholder('Hazelaarstraat 15'),
                TextInput::make('contact_address_line2')->label('Adres — regel 2')->placeholder('4043PG OPHEUSDEN'),
                TextInput::make('company_vat')->label('BTW-nummer')->placeholder('NL001914453B09'),
                TextInput::make('company_coc')->label('KVK-nummer')->placeholder('11063125'),
            ])->columns(2),

            Section::make('Social media')
                ->description('Laat leeg om een icoon te verbergen.')
                ->schema([
                    // Plain TextInputs — no ->url() rule, so partial or relative values still save.
                    TextInput::make('social_facebook')->label('Facebook'),
                    TextInput::make('social_twitter')->label('X / Twitter'),
                    TextInput::make('social_linkedin')->label('LinkedIn'),
                    TextInput::make('social_instagram')->label('Instagram'),
                ])->columns(2),

            Section::make('Reviews')
                ->description('Gebruikt door de reviews-widget en de balk bovenaan.')
                ->schema([
                    TextInput::make('trustpilot_url')->label('Trustpilot-link'),
                    TextInput::make('trustpilot_score')->label('Score')->placeholder('Great'),
                    TextInput::make('trustpilot_count')->label('Aantal reviews')->numeric(),
                ])->columns(3),
        ])->statePath('data');
    }

    /**
     * FileUpload doesn't recognise the seeded design-asset paths (e.g.
     * "/images/design/logo.png") as existing files, since they're not on the
     * uploads disk — so an untouched logo field comes back empty. Only
     * overwrite these when staff actually pick a new file; leave them as-is
     * otherwise so saving the rest of the form can't wipe the current logo.
     */
    private const PRESERVE_WHEN_EMPTY = ['logo', 'logo_white'];

    /** Never exposed via /api/v1/settings — saved with is_public = false. */
    private const PRIVATE_KEYS = [];

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $value = (string) ($value ?? '');

            if ($value === '' && in_array($key, self::PRESERVE_WHEN_EMPTY, true)) {
                continue;
            }

            Setting::set($key, $value, ! in_array($key, self::PRIVATE_KEYS, true));
        }

        Notification::make()->title('Instellingen opgeslagen')->success()->send();
    }
}
