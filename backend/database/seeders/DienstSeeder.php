<?php

namespace Database\Seeders;

use App\Models\Dienst;
use Illuminate\Database\Seeder;

/**
 * The five diensten from the design's "Boxes" section (Figma 31789:28) —
 * copy, accent colours and photos exactly as designed.
 *
 * Images point at the frontend's own /images/design/ directory. Filament's
 * FileUpload can't represent those paths, so they are protected on save by
 * App\Support\MediaState and fall back on render via designDefaults.ts.
 */
class DienstSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->diensten() as $dienst) {
            Dienst::updateOrCreate(['slug' => $dienst['slug']], $dienst + ['is_published' => true]);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function diensten(): array
    {
        return [
            [
                'slug' => 'ict-support',
                'label' => 'ICT Support',
                'title' => 'Uw betrouwbare IT-partner',
                'excerpt' => '<p>Heeft u problemen met uw computer, laptop of netwerk? Wij bieden snelle ondersteuning voor zowel particulieren als bedrijven. Van storingen en onderhoud tot systeembeheer en preventieve controles: wij zorgen ervoor dat uw IT-omgeving betrouwbaar blijft werken.</p>',
                'accent_color' => '#1e62d4',
                'accent_label_color' => '#1e62d4',
                'image' => '/images/design/dienst-ict-support.png',
                'features' => [
                    ['label' => 'Hardware & software ondersteuning'],
                    ['label' => 'Helpdesk & remote support'],
                    ['label' => 'Preventief onderhoud'],
                    ['label' => 'Systeembeheer'],
                ],
                'cta_label' => 'Meer informatie',
                'cta_href' => '/diensten/ict-support',
                'sort_order' => 0,
            ],
            [
                'slug' => 'microsoft-365',
                'label' => 'Microsoft 365',
                'title' => 'Slim samenwerken met Microsoft 365',
                'excerpt' => '<p>Werk efficiënter met Microsoft 365. Wij verzorgen installatie, migratie en beheer van e-mail, cloudopslag en samenwerkingsplatformen, zodat u overal veilig kunt werken.</p>',
                // The design gives this box a darker label than its button.
                'accent_color' => '#fcb040',
                'accent_label_color' => '#ee8e00',
                'image' => '/images/design/dienst-microsoft-365.png',
                'features' => [
                    ['label' => 'Microsoft 365 installatie'],
                    ['label' => 'E-mail & Outlook'],
                    ['label' => 'Cloudopslag'],
                    ['label' => 'Beheer & ondersteuning'],
                ],
                'cta_label' => 'Meer informatie',
                'cta_href' => '/diensten/microsoft-365',
                'sort_order' => 1,
            ],
            [
                'slug' => 'website-hosting',
                'label' => 'Website & Hosting',
                'title' => 'Professionele websites met betrouwbare hosting',
                'excerpt' => '<p>Van een moderne bedrijfswebsite tot veilige webhosting. Wij ontwerpen, bouwen en onderhouden websites die snel, veilig en eenvoudig te beheren zijn, zodat uw online aanwezigheid altijd optimaal presteert.</p>',
                'accent_color' => '#43a047',
                'accent_label_color' => '#43a047',
                'image' => '/images/design/dienst-website-hosting.png',
                'features' => [
                    ['label' => 'Professioneel webdesign'],
                    ['label' => 'Betrouwbare webhosting'],
                    ['label' => 'Website onderhoud'],
                    ['label' => 'Domeinregistratie & e-mail'],
                ],
                'cta_label' => 'Bekijk onze websites',
                'cta_href' => '/diensten/website-hosting',
                'sort_order' => 2,
            ],
            [
                'slug' => 'cloud-diensten',
                'label' => 'Cloud Diensten',
                'title' => 'Veilig werken in de cloud',
                'excerpt' => '<p>Profiteer van veilige cloudoplossingen voor opslag, back-ups en samenwerking. Wij helpen u met betrouwbare cloudservices die meegroeien met uw organisatie.</p>',
                'accent_color' => '#6c63ff',
                'accent_label_color' => '#6c63ff',
                'image' => '/images/design/dienst-cloud-diensten.png',
                'features' => [
                    ['label' => 'Cloudopslag'],
                    ['label' => 'Back-up oplossingen'],
                    ['label' => 'Beveiliging'],
                    ['label' => 'Cloudbeheer'],
                ],
                'cta_label' => 'Meer informatie',
                'cta_href' => '/diensten/cloud-diensten',
                'sort_order' => 3,
            ],
            [
                'slug' => 'netwerk-telefonie',
                'label' => 'Netwerk & Telefonie',
                'title' => 'Altijd verbonden',
                'excerpt' => '<p>Van betrouwbare netwerken en glasvezelverbindingen tot moderne VoIP-telefonie. Wij zorgen voor snelle, veilige en stabiele communicatie, thuis én op kantoor.</p>',
                'accent_color' => '#ff8900',
                'accent_label_color' => '#ff8900',
                'image' => '/images/design/dienst-netwerk-telefonie.png',
                'features' => [
                    ['label' => 'Netwerken'],
                    ['label' => 'Glasvezel & internet'],
                    ['label' => 'VoIP telefonie'],
                    ['label' => 'Netwerkbeheer'],
                ],
                'cta_label' => 'Meer informatie',
                'cta_href' => '/diensten/netwerk-telefonie',
                'sort_order' => 4,
            ],
        ];
    }
}
