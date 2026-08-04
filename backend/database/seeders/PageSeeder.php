<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * The CMS pages that drive both the routes and the header menu, plus the
 * homepage's widgets in design order. Widgets are appended here as each
 * section is sliced.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $home = $this->page([
            'name' => 'Home',
            'title' => 'Uw betrouwbare ICT-partner voor thuis en op kantoor',
            'slug' => 'home',
            'menu_sort' => 0,
            'seo' => [
                'title' => 'Johan Willemsen Computers — uw betrouwbare ICT-partner in Opheusden',
                'description' => 'ICT-specialist in Opheusden: computerreparatie, netwerkbeheer, Microsoft 365, cloudoplossingen, websites en hosting.',
            ],
        ]);

        $overOns = $this->page([
            'name' => 'Over ons',
            'title' => 'Wij zijn Johan Willemsen Computers',
            'slug' => 'over-ons',
            'menu_sort' => 1,
            'seo' => [
                'title' => 'Over Johan Willemsen Computers — ICT-specialist in Opheusden',
                'description' => 'Al meer dan 25 jaar de betrouwbare ICT-specialist voor particulieren en bedrijven in Opheusden en omgeving.',
            ],
        ]);
        $this->blocks($overOns, $this->overOnsBlocks());

        $diensten = $this->page([
            'name' => 'Diensten',
            'title' => 'Onze diensten',
            'slug' => 'diensten',
            'menu_sort' => 2,
            // Auto-lists the published Diensten CPT as submenu items.
            'menu_dropdown_type' => 'dropdown',
            'menu_diensten' => true,
            'menu_dropdown_cta' => ['label' => 'Alle diensten', 'href' => '/diensten'],
            'seo' => [
                'title' => 'Onze diensten — Johan Willemsen Computers',
                'description' => 'ICT-support, Microsoft 365 & cloud, netwerk & telefonie, websites en hosting. Alles voor uw computer onder één dak.',
            ],
        ]);
        $this->blocks($diensten, $this->dienstenBlocks());

        // Dienst pages, nested under Diensten in the menu. Each follows the
        // Website & Hosting structure (Figma 31847:1690): compact hero →
        // text+image → 3-column service cards → CTA banner. Only Website &
        // Hosting also gets the projects grid, since that is the one with a
        // portfolio behind it.
        foreach ($this->dienstPages() as $sort => $dienstPage) {
            $page = $this->page([
                'name' => $dienstPage['name'],
                'title' => $dienstPage['title'],
                'slug' => $dienstPage['slug'],
                'parent_id' => $diensten->id,
                'menu_visibility' => false, // the Diensten dropdown lists the CPT itself
                'menu_sort' => $sort,
                'seo' => $dienstPage['seo'],
            ]);

            $this->blocks($page, $dienstPage['blocks']);
        }

        $this->page([
            'name' => 'Blog',
            'title' => 'Blog',
            'slug' => 'blog',
            'menu_sort' => 3,
            'seo' => [
                'title' => 'Blog — IT-tips en nieuws van Johan Willemsen Computers',
                'description' => 'Praktische IT-kennis voor iedereen: tips over hardware, software, cloud, netwerk en beveiliging, in gewone taal uitgelegd.',
            ],
        ]);

        $contact = $this->page([
            'name' => 'Contact',
            'title' => 'Contact',
            'slug' => 'contact',
            'menu_sort' => 4,
            'seo' => [
                'title' => 'Contact — Johan Willemsen Computers in Opheusden',
                'description' => 'Neem contact op met Johan Willemsen Computers in Opheusden. Bel, mail of stuur het formulier in — u krijgt altijd binnen één werkdag antwoord.',
            ],
        ]);
        $this->blocks($contact, $this->contactBlocks());

        // Legal pages — reachable from the footer, hidden from the header menu.
        $voorwaarden = $this->page([
            'name' => 'Terms and Conditions',
            'title' => 'Algemene voorwaarden',
            'slug' => 'algemene-voorwaarden',
            'menu_visibility' => false,
            'menu_sort' => 10,
            'seo' => [
                'title' => 'Algemene voorwaarden — Johan Willemsen Computers',
                'description' => 'De algemene voorwaarden die gelden voor alle offertes, opdrachten en leveringen van Johan Willemsen Computers.',
            ],
        ]);
        $this->blocks($voorwaarden, $this->voorwaardenBlocks());

        $privacy = $this->page([
            'name' => 'Privacy Policy',
            'title' => 'Privacyverklaring',
            'slug' => 'privacy-policy',
            'menu_visibility' => false,
            'menu_sort' => 11,
            'seo' => [
                'title' => 'Privacyverklaring — Johan Willemsen Computers',
                'description' => 'Hoe Johan Willemsen Computers omgaat met uw persoonsgegevens: welke gegevens wij verwerken, waarom, hoe lang en welke rechten u heeft.',
            ],
        ]);
        $this->blocks($privacy, $this->privacyBlocks());

        $this->blocks($home, $this->homeBlocks());
    }

    /**
     * The Over ons page (Figma 31837:550). Reuses the hero (compact/left),
     * solution cards and CTA banner from the homepage.
     *
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function overOnsBlocks(): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    // *…* renders in the accent colour.
                    'title' => 'Wij zijn *Johan Willemsen* Computers',
                    'body' => 'Johan Willemsen Computers is al meer dan 25 jaar de betrouwbare ICT-specialist voor particulieren en bedrijven in Opheusden en de omgeving. Wij bieden persoonlijke service, eerlijk advies en professionele oplossingen.',
                    'size' => 'compact',
                    'align' => 'left',
                    'pattern' => 'right',
                    'image' => '',
                    'video' => '',
                    'show_overlay' => false,
                    'buttons' => [],
                ],
            ],

            [
                'type' => 'text_image',
                'data' => [
                    'label' => 'ONZE MISSIE',
                    'title' => 'IT die gewoon werkt, voor elk bedrijf',
                    'body' => '<p>Wij geloven dat goede IT-ondersteuning niet is voorbehouden aan grote bedrijven. Elke ondernemer verdient een betrouwbare IT-partner die snel reageert, eerlijk adviseert en betaalbare oplossingen biedt.</p><p>Vanaf dag één staat persoonlijk contact centraal. Geen anonieme helpdesk, maar een vertrouwde adviseur die uw bedrijf door en door kent.</p>',
                    'image' => '/images/design/over-ons-missie.png',
                    'image_side' => 'right',
                    'accent' => '#e8891a',
                    'accent_label_color' => '#e8891a',
                    'features' => [
                        ['label' => 'Hardware & software ondersteuning'],
                        ['label' => 'Helpdesk & remote support'],
                        ['label' => 'Preventief onderhoud'],
                        ['label' => 'Systeembeheer'],
                    ],
                    'cta_label' => '',
                    'cta_href' => '',
                ],
            ],

            [
                'type' => 'stats',
                'data' => [
                    'accent' => '#ffa425',
                    'stats' => [
                        ['icon' => '/images/design/stat-klanten.svg', 'value' => '2.500+', 'label' => 'Tevreden klanten'],
                        ['icon' => '/images/design/stat-expertise.svg', 'value' => '15+', 'label' => 'Jaar expertise'],
                        ['icon' => '/images/design/stat-responstijd.svg', 'value' => '<24u', 'label' => 'Gemiddelde responstijd'],
                        ['icon' => '/images/design/stat-bedrijven.svg', 'value' => '200+', 'label' => 'Bedrijven ondersteund'],
                    ],
                ],
            ],

            [
                'type' => 'solution_cards',
                'data' => [
                    'heading' => 'Waarom klanten voor ons kiezen',
                    'cards' => [
                        [
                            'icon' => '/images/design/icon-betrouwbaar.svg',
                            'title' => 'Betrouwbaar',
                            'text' => 'ICT Waarborg gecertificeerd. Eerlijk advies zonder verborgen kosten.',
                            'cta_label' => '',
                            'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-snel.svg',
                            'title' => 'Snel',
                            'text' => 'Veelal dezelfde dag geholpen. Remote ondersteuning via TeamViewer.',
                            'cta_label' => '',
                            'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-persoonlijk.svg',
                            'title' => 'Persoonlijk',
                            'text' => 'Geen callcenter, maar direct contact met een expert die u kent.',
                            'cta_label' => '',
                            'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-compleet.svg',
                            'title' => 'Compleet',
                            'text' => 'Van hardware tot cloud — alles voor uw computer onder één dak.',
                            'cta_label' => '',
                            'cta_href' => '',
                        ],
                    ],
                ],
            ],

            [
                'type' => 'cta_banner',
                'data' => [
                    'title' => 'Direct hulp nodig?',
                    'body' => 'Heeft u een technisch probleem? Met TeamViewer kunnen wij veilig op afstand verbinding maken met uw computer en u direct helpen.',
                    'cta_label' => 'Start remote support',
                    'cta_href' => '/contact',
                    'show_pattern' => true,
                ],
            ],
        ];
    }

    /**
     * The Diensten overview. No dedicated Figma frame exists for it, so it is
     * composed from widgets already sliced elsewhere and driven entirely by the
     * Diensten CPT — adding a dienst in the admin adds it here automatically.
     *
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function dienstenBlocks(): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'title' => 'Onze *diensten*',
                    'body' => 'Van computerreparatie en netwerkbeheer tot Microsoft 365, cloudoplossingen, websites en hosting. Alles voor uw computer onder één dak.',
                    'size' => 'compact',
                    'align' => 'left',
                    'pattern' => 'right',
                    'image' => '',
                    'video' => '',
                    'show_overlay' => false,
                    'buttons' => [],
                ],
            ],

            [
                'type' => 'dienst_boxes',
                'data' => [
                    'heading' => 'Slimme technologie, persoonlijke service',
                    'intro' => 'Met jarenlange ervaring leveren wij complete ICT-oplossingen die aansluiten op uw wensen. Van installatie en beheer tot onderhoud en ondersteuning: wij regelen het voor u.',
                    'dienst_ids' => [],
                    'count' => 10,
                ],
            ],

            [
                'type' => 'cta_banner',
                'data' => [
                    'title' => 'Direct hulp nodig?',
                    'body' => 'Heeft u een technisch probleem? Met TeamViewer kunnen wij veilig op afstand verbinding maken met uw computer en u direct helpen.',
                    'cta_label' => 'Start remote support',
                    'cta_href' => '/contact',
                    'show_pattern' => true,
                ],
            ],
        ];
    }

    /**
     * One page per dienst, all following the Website & Hosting layout.
     *
     * @return array<int, array{name: string, title: string, slug: string, seo: array<string, string>, blocks: array<int, array{type: string, data: array<string, mixed>}>}>
     */
    private function dienstPages(): array
    {
        return [
            [
                'name' => 'Website & Hosting',
                'title' => 'Professionele websites & webshops',
                'slug' => 'website-hosting',
                'seo' => [
                    'title' => 'Webdesign & hosting — Johan Willemsen Computers',
                    'description' => 'Professionele websites en webshops met betrouwbare hosting, onderhoud en SEO. Bekijk onze gerealiseerde projecten.',
                ],
                'blocks' => $this->websiteHostingBlocks(),
            ],
            [
                'name' => 'ICT Support',
                'title' => 'ICT-support & beheer',
                'slug' => 'ict-support',
                'seo' => [
                    'title' => 'ICT-support & beheer — Johan Willemsen Computers',
                    'description' => 'Snelle hulp bij storingen, installaties en dagelijks beheer. Helpdesk, remote support en preventief onderhoud.',
                ],
                'blocks' => $this->dienstPageBlocks('ict-support', 'Hulp die er meteen is', [
                    ['icon' => '/images/design/icon-support.svg', 'title' => 'Helpdesk', 'text' => 'Direct een expert aan de lijn die uw situatie kent. Geen wachtrij, geen script.'],
                    ['icon' => '/images/design/icon-snel.svg', 'title' => 'Remote support', 'text' => 'Via TeamViewer kijken wij veilig mee en lossen we problemen vaak dezelfde dag op.'],
                    ['icon' => '/images/design/icon-onderhoud.svg', 'title' => 'Preventief onderhoud', 'text' => 'Periodieke controles en updates, zodat storingen voorkomen worden.'],
                    ['icon' => '/images/design/icon-betrouwbaar.svg', 'title' => 'Systeembeheer', 'text' => 'Beheer van werkplekken, servers en gebruikers — volledig uit handen genomen.'],
                    ['icon' => '/images/design/icon-web.svg', 'title' => 'Hardware & software', 'text' => 'Advies, levering en installatie van computers, randapparatuur en licenties.'],
                    ['icon' => '/images/design/icon-beveiliging.svg', 'title' => 'Beveiliging', 'text' => 'Virusscanners, updates en back-ups die uw werkplekken beschermd houden.'],
                ]),
            ],
            [
                'name' => 'Microsoft 365',
                'title' => 'Slim samenwerken met Microsoft 365',
                'slug' => 'microsoft-365',
                'seo' => [
                    'title' => 'Microsoft 365 & cloud — Johan Willemsen Computers',
                    'description' => 'Installatie, migratie en beheer van Microsoft 365: e-mail, Outlook, cloudopslag en veilig samenwerken.',
                ],
                'blocks' => $this->dienstPageBlocks('microsoft-365', 'Alles uit Microsoft 365 halen', [
                    ['icon' => '/images/design/icon-cloud.svg', 'title' => 'Installatie & migratie', 'text' => 'Overstappen zonder downtime, met behoud van al uw e-mail en documenten.'],
                    ['icon' => '/images/design/icon-webhosting.svg', 'title' => 'E-mail & Outlook', 'text' => 'Professionele e-mail op uw eigen domein, overal en op elk apparaat.'],
                    ['icon' => '/images/design/icon-onderhoud.svg', 'title' => 'Cloudopslag', 'text' => 'OneDrive en SharePoint ingericht zodat bestanden altijd terugvindbaar zijn.'],
                    ['icon' => '/images/design/icon-persoonlijk.svg', 'title' => 'Samenwerken', 'text' => 'Teams en gedeelde agenda\'s, ingericht op hoe uw organisatie werkt.'],
                    ['icon' => '/images/design/icon-beveiliging.svg', 'title' => 'Beveiliging', 'text' => 'Meervoudige verificatie en rechtenbeheer voor al uw gebruikers.'],
                    ['icon' => '/images/design/icon-support.svg', 'title' => 'Beheer & support', 'text' => 'Wij beheren licenties en gebruikers, u belt ons als er iets is.'],
                ]),
            ],
            [
                'name' => 'Cloud Diensten',
                'title' => 'Veilig werken in de cloud',
                'slug' => 'cloud-diensten',
                'seo' => [
                    'title' => 'Cloud diensten — Johan Willemsen Computers',
                    'description' => 'Cloudopslag, back-up en beveiliging. Betrouwbare cloudservices die meegroeien met uw organisatie.',
                ],
                'blocks' => $this->dienstPageBlocks('cloud-diensten', 'De cloud, goed geregeld', [
                    ['icon' => '/images/design/icon-cloud.svg', 'title' => 'Cloudopslag', 'text' => 'Uw bestanden veilig beschikbaar, op kantoor en onderweg.'],
                    ['icon' => '/images/design/icon-onderhoud.svg', 'title' => 'Back-up oplossingen', 'text' => 'Automatische dagelijkse back-ups met periodieke hersteltests.'],
                    ['icon' => '/images/design/icon-beveiliging.svg', 'title' => 'Beveiliging', 'text' => 'Versleutelde opslag binnen de EU en streng rechtenbeheer.'],
                    ['icon' => '/images/design/icon-betrouwbaar.svg', 'title' => 'Cloudbeheer', 'text' => 'Wij houden de omgeving up-to-date en bewaken het verbruik.'],
                    ['icon' => '/images/design/icon-snel.svg', 'title' => 'Migratie', 'text' => 'Van lokale server naar cloud, gefaseerd en zonder verlies van data.'],
                    ['icon' => '/images/design/icon-support.svg', 'title' => 'Support', 'text' => 'Een vast aanspreekpunt dat uw cloudomgeving door en door kent.'],
                ]),
            ],
            [
                'name' => 'Netwerk & Telefonie',
                'title' => 'Altijd verbonden',
                'slug' => 'netwerk-telefonie',
                'seo' => [
                    'title' => 'Netwerk & telefonie — Johan Willemsen Computers',
                    'description' => 'Betrouwbare netwerken, glasvezel en VoIP-telefonie voor thuis en op kantoor.',
                ],
                'blocks' => $this->dienstPageBlocks('netwerk-telefonie', 'Een netwerk waar u op kunt bouwen', [
                    ['icon' => '/images/design/icon-network.svg', 'title' => 'Bedrijfsnetwerken', 'text' => 'Bekabeling, switches en wifi die overal in het pand bereik geven.'],
                    ['icon' => '/images/design/icon-webhosting.svg', 'title' => 'Glasvezel & internet', 'text' => 'Advies over de juiste verbinding en volledige installatie.'],
                    ['icon' => '/images/design/icon-phone.svg', 'title' => 'VoIP telefonie', 'text' => 'Moderne telefonie met keuzemenu\'s, doorschakelen en bereikbaarheid op elk apparaat.'],
                    ['icon' => '/images/design/icon-betrouwbaar.svg', 'title' => 'Netwerkbeheer', 'text' => 'Monitoring en onderhoud, zodat storingen opvallen voordat u ze merkt.'],
                    ['icon' => '/images/design/icon-beveiliging.svg', 'title' => 'Firewall & beveiliging', 'text' => 'Uw netwerk afgeschermd tegen ongewenst verkeer van buitenaf.'],
                    ['icon' => '/images/design/icon-snel.svg', 'title' => 'Wifi optimalisatie', 'text' => 'Metingen en accesspoints op de juiste plek voor stabiel bereik.'],
                ]),
            ],
        ];
    }

    /**
     * The shared dienst-page shape. The text+image block mirrors the dienst's
     * own CPT content so the page and the homepage box stay in step.
     *
     * @param  array<int, array<string, string>>  $cards
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function dienstPageBlocks(string $slug, string $cardsHeading, array $cards): array
    {
        $dienst = collect($this->dienstenContent())->firstWhere('slug', $slug);

        return [
            [
                'type' => 'hero',
                'data' => [
                    'title' => $dienst['hero_title'],
                    'body' => $dienst['hero_body'],
                    'size' => 'compact',
                    'align' => 'left',
                    'pattern' => 'right',
                    'image' => '',
                    'video' => '',
                    'show_overlay' => false,
                    'buttons' => [],
                ],
            ],

            [
                'type' => 'text_image',
                'data' => [
                    'label' => $dienst['label'],
                    'title' => $dienst['title'],
                    'body' => $dienst['body'],
                    'image' => $dienst['image'],
                    'image_side' => 'right',
                    'accent' => $dienst['accent'],
                    'accent_label_color' => $dienst['accent'],
                    'features' => $dienst['features'],
                    'cta_label' => 'Neem contact op',
                    'cta_href' => '/contact',
                ],
            ],

            [
                'type' => 'solution_cards',
                'data' => [
                    'heading' => $cardsHeading,
                    'intro' => $dienst['cards_intro'],
                    'columns' => 3,
                    'cards' => array_map(
                        fn (array $c): array => $c + ['cta_label' => '', 'cta_href' => ''],
                        $cards,
                    ),
                ],
            ],

            [
                'type' => 'cta_banner',
                'data' => [
                    'title' => 'Direct hulp nodig?',
                    'body' => 'Heeft u een technisch probleem? Met TeamViewer kunnen wij veilig op afstand verbinding maken met uw computer en u direct helpen.',
                    'cta_label' => 'Start remote support',
                    'cta_href' => '/contact',
                    'show_pattern' => true,
                ],
            ],
        ];
    }

    /** Copy shared between the dienst pages and their CPT records. */
    private function dienstenContent(): array
    {
        return [
            [
                'slug' => 'ict-support',
                'label' => 'ICT Support',
                'accent' => '#1e62d4',
                'image' => '/images/design/dienst-ict-support.png',
                'hero_title' => 'ICT-support & *beheer*',
                'hero_body' => 'Snelle hulp bij storingen, installaties en dagelijks beheer — voor particulieren en bedrijven in Opheusden en omgeving.',
                'title' => 'Uw betrouwbare IT-partner',
                'body' => '<p>Heeft u problemen met uw computer, laptop of netwerk? Wij bieden snelle ondersteuning voor zowel particulieren als bedrijven. Van storingen en onderhoud tot systeembeheer en preventieve controles: wij zorgen ervoor dat uw IT-omgeving betrouwbaar blijft werken.</p>',
                'cards_intro' => 'Alles wat u nodig heeft om zorgeloos te werken, van één vast aanspreekpunt.',
                'features' => [
                    ['label' => 'Hardware & software ondersteuning'],
                    ['label' => 'Helpdesk & remote support'],
                    ['label' => 'Preventief onderhoud'],
                    ['label' => 'Systeembeheer'],
                ],
            ],
            [
                'slug' => 'microsoft-365',
                'label' => 'Microsoft 365',
                'accent' => '#fcb040',
                'image' => '/images/design/dienst-microsoft-365.png',
                'hero_title' => 'Microsoft 365 & *cloud*',
                'hero_body' => 'Installatie, migratie en beheer van de complete Microsoft 365-omgeving, zodat u overal veilig kunt samenwerken.',
                'title' => 'Slim samenwerken met Microsoft 365',
                'body' => '<p>Werk efficiënter met Microsoft 365. Wij verzorgen installatie, migratie en beheer van e-mail, cloudopslag en samenwerkingsplatformen, zodat u overal veilig kunt werken.</p>',
                'cards_intro' => 'Van migratie tot dagelijks beheer — wij richten Microsoft 365 in op uw manier van werken.',
                'features' => [
                    ['label' => 'Microsoft 365 installatie'],
                    ['label' => 'E-mail & Outlook'],
                    ['label' => 'Cloudopslag'],
                    ['label' => 'Beheer & ondersteuning'],
                ],
            ],
            [
                'slug' => 'cloud-diensten',
                'label' => 'Cloud Diensten',
                'accent' => '#6c63ff',
                'image' => '/images/design/dienst-cloud-diensten.png',
                'hero_title' => 'Veilig werken in de *cloud*',
                'hero_body' => 'Cloudopslag, back-up en beveiliging. Uw data altijd beschikbaar en beschermd, waar u ook werkt.',
                'title' => 'Veilig werken in de cloud',
                'body' => '<p>Profiteer van veilige cloudoplossingen voor opslag, back-ups en samenwerking. Wij helpen u met betrouwbare cloudservices die meegroeien met uw organisatie.</p>',
                'cards_intro' => 'Betrouwbare cloudservices die meegroeien met uw organisatie.',
                'features' => [
                    ['label' => 'Cloudopslag'],
                    ['label' => 'Back-up oplossingen'],
                    ['label' => 'Beveiliging'],
                    ['label' => 'Cloudbeheer'],
                ],
            ],
            [
                'slug' => 'netwerk-telefonie',
                'label' => 'Netwerk & Telefonie',
                'accent' => '#ff8900',
                'image' => '/images/design/dienst-netwerk-telefonie.png',
                'hero_title' => 'Netwerk & *telefonie*',
                'hero_body' => 'Betrouwbare netwerken, glasvezelverbindingen en moderne VoIP-telefonie voor thuis én op kantoor.',
                'title' => 'Altijd verbonden',
                'body' => '<p>Van betrouwbare netwerken en glasvezelverbindingen tot moderne VoIP-telefonie. Wij zorgen voor snelle, veilige en stabiele communicatie, thuis én op kantoor.</p>',
                'cards_intro' => 'Snelle, veilige en stabiele communicatie — thuis en op kantoor.',
                'features' => [
                    ['label' => 'Netwerken'],
                    ['label' => 'Glasvezel & internet'],
                    ['label' => 'VoIP telefonie'],
                    ['label' => 'Netwerkbeheer'],
                ],
            ],
        ];
    }

    /**
     * The Website & Hosting dienst page (Figma 31847:1690). Reuses the compact
     * hero, the text+image block, the cards grid (3 columns) and the CTA banner;
     * the projects grid lists the Projecten CPT.
     *
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function websiteHostingBlocks(): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'title' => 'Professionele websites & webshops',
                    'body' => 'Van een moderne bedrijfswebsite tot een complete webshop. Wij ontwerpen, bouwen en hosten websites die snel, veilig en eenvoudig te beheren zijn.',
                    'size' => 'compact',
                    'align' => 'left',
                    'pattern' => 'right',
                    'image' => '',
                    'video' => '',
                    'show_overlay' => false,
                    'buttons' => [],
                ],
            ],

            [
                'type' => 'text_image',
                'data' => [
                    'label' => 'Website & Hosting',
                    'title' => 'Professionele websites met betrouwbare hosting',
                    'body' => '<p>Van een moderne bedrijfswebsite tot veilige webhosting. Wij ontwerpen, bouwen en onderhouden websites die snel, veilig en eenvoudig te beheren zijn, zodat uw online aanwezigheid altijd optimaal presteert.</p>',
                    'image' => '/images/design/dienst-website-hosting.png',
                    'image_side' => 'right',
                    'accent' => '#43a047',
                    'accent_label_color' => '#43a047',
                    'features' => [
                        ['label' => 'Professioneel webdesign'],
                        ['label' => 'Betrouwbare webhosting'],
                        ['label' => 'Website onderhoud'],
                        ['label' => 'Domeinregistratie & e-mail'],
                    ],
                    'cta_label' => 'Bekijk onze websites',
                    'cta_href' => '#projecten',
                ],
            ],

            [
                'type' => 'solution_cards',
                'data' => [
                    'heading' => 'Webdesign diensten',
                    'intro' => 'Een compleet pakket aan webdiensten voor particulieren en bedrijven in de regio.',
                    'columns' => 3,
                    'cards' => [
                        [
                            'icon' => '/images/design/icon-website-ontwerp.svg',
                            'title' => 'Website ontwerp',
                            'text' => 'Op maat gemaakte, aantrekkelijke websites die uw bedrijf professioneel presenteren. Mobielvriendelijk en snel.',
                            'cta_label' => '', 'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-webshop.svg',
                            'title' => 'Webshop',
                            'text' => 'Complete e-commerce oplossingen met veilig betalen, voorraadbeheer en eenvoudig productbeheer via WordPress & WooCommerce.',
                            'cta_label' => '', 'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-onderhoud.svg',
                            'title' => 'Onderhoud & updates',
                            'text' => 'Wij houden uw website veilig, up-to-date en snel met regelmatig onderhoud en content-updates.',
                            'cta_label' => '', 'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-webhosting.svg',
                            'title' => 'Webhosting',
                            'text' => 'Betrouwbare hosting met hoge uptime, SSL-certificaat, dagelijkse backups en technische ondersteuning.',
                            'cta_label' => '', 'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-seo.svg',
                            'title' => 'SEO optimalisatie',
                            'text' => 'Beter gevonden worden in Google met technische SEO, snelle laadtijden en geoptimaliseerde content.',
                            'cta_label' => '', 'cta_href' => '',
                        ],
                        [
                            'icon' => '/images/design/icon-beveiliging.svg',
                            'title' => 'Beveiliging',
                            'text' => 'SSL-certificaten, beveiligingsscans en bescherming tegen hackers en malware voor uw website.',
                            'cta_label' => '', 'cta_href' => '',
                        ],
                    ],
                ],
            ],

            [
                'type' => 'projects',
                'data' => [
                    'heading' => 'Oplossingen waar we trots op zijn',
                    'intro' => 'Bekijk een selectie van websites, ICT-projecten en technische oplossingen die wij hebben gerealiseerd.',
                    'layout' => 'grid',
                    'columns' => 3,
                    'project_ids' => [],
                    'count' => 9,
                    'cta_label' => 'Meer weergeven',
                    'cta_href' => '/projecten',
                ],
            ],

            [
                'type' => 'cta_banner',
                'data' => [
                    'title' => 'Direct hulp nodig?',
                    'body' => 'Heeft u een technisch probleem? Met TeamViewer kunnen wij veilig op afstand verbinding maken met uw computer en u direct helpen.',
                    'cta_label' => 'Start remote support',
                    'cta_href' => '/contact',
                    'show_pattern' => true,
                ],
            ],
        ];
    }

    /** @return array<int, array{type: string, data: array<string, mixed>}> */
    private function contactBlocks(): array
    {
        return [
            [
                'type' => 'contact_form',
                'data' => [
                    'heading' => 'Neem contact op',
                    'intro' => 'Heeft u een vraag of wilt u een vrijblijvende offerte? Laat uw gegevens achter en wij nemen zo snel mogelijk contact met u op.',
                    'show_details' => true,
                    'details_heading' => 'Onze gegevens',
                    'submit_label' => 'Verstuur bericht',
                    'subjects' => [
                        ['label' => 'ICT Support & Beheer'],
                        ['label' => 'Microsoft 365 & Cloud'],
                        ['label' => 'Netwerk & Telefonie'],
                        ['label' => 'Website & Hosting'],
                        ['label' => 'Offerte aanvragen'],
                        ['label' => 'Iets anders'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Placeholder legal copy so the page is never blank. It is deliberately
     * generic — the real terms are a legal document the client supplies, and
     * staff replace this from the Tekstblok in the admin.
     */
    private function voorwaardenBlocks(): array
    {
        return [
            [
                'type' => 'rich_text',
                'data' => [
                    'heading' => 'Algemene voorwaarden',
                    'body' => <<<'HTML'
                        <p>Deze algemene voorwaarden zijn van toepassing op alle offertes, opdrachten en leveringen van Johan Willemsen Computers, gevestigd te Opheusden.</p>
                        <h2>1. Offertes en overeenkomsten</h2>
                        <p>Alle offertes zijn vrijblijvend en dertig dagen geldig, tenzij anders vermeld. Een overeenkomst komt tot stand zodra u de offerte schriftelijk of per e-mail heeft bevestigd.</p>
                        <h2>2. Uitvoering van de werkzaamheden</h2>
                        <p>Wij voeren de werkzaamheden naar beste inzicht en vermogen uit. Genoemde termijnen zijn indicatief; overschrijding geeft geen recht op ontbinding of schadevergoeding.</p>
                        <h2>3. Prijzen en betaling</h2>
                        <p>Alle prijzen zijn exclusief btw, tenzij uitdrukkelijk anders vermeld. Facturen dienen binnen veertien dagen na factuurdatum te zijn voldaan.</p>
                        <h2>4. Aansprakelijkheid</h2>
                        <p>Onze aansprakelijkheid is beperkt tot het factuurbedrag van de betreffende opdracht. Wij zijn niet aansprakelijk voor gegevensverlies; het maken van een back-up blijft de verantwoordelijkheid van de klant, tenzij hierover schriftelijke afspraken zijn gemaakt.</p>
                        <h2>5. Garantie</h2>
                        <p>Op geleverde hardware geldt de fabrieksgarantie. Op uitgevoerde reparaties geven wij drie maanden garantie op het uitgevoerde werk, mits het defect dezelfde oorzaak heeft.</p>
                        <h2>6. Toepasselijk recht</h2>
                        <p>Op alle overeenkomsten is Nederlands recht van toepassing. Geschillen worden voorgelegd aan de bevoegde rechter in het arrondissement Gelderland.</p>
                        HTML,
                ],
            ],
        ];
    }

    /** Placeholder privacy statement — see {@see voorwaardenBlocks()}. */
    private function privacyBlocks(): array
    {
        return [
            [
                'type' => 'rich_text',
                'data' => [
                    'heading' => 'Privacyverklaring',
                    'body' => <<<'HTML'
                        <p>Johan Willemsen Computers hecht veel waarde aan uw privacy. In deze verklaring leggen wij uit welke persoonsgegevens wij verwerken, waarom wij dat doen en welke rechten u heeft.</p>
                        <h2>Welke gegevens wij verwerken</h2>
                        <p>Wij verwerken de gegevens die u zelf aan ons verstrekt: uw naam, e-mailadres, telefoonnummer, bedrijfsnaam en de inhoud van uw bericht. Bij een reparatie of beheeropdracht kunnen wij daarnaast technische gegevens van uw apparatuur vastleggen.</p>
                        <h2>Waarom wij deze gegevens verwerken</h2>
                        <ul>
                            <li>Om uw vraag of offerteaanvraag te beantwoorden;</li>
                            <li>Om een opdracht uit te voeren en te factureren;</li>
                            <li>Om te voldoen aan onze wettelijke administratieplicht.</li>
                        </ul>
                        <h2>Hoe lang wij gegevens bewaren</h2>
                        <p>Contactaanvragen bewaren wij maximaal twee jaar. Administratieve gegevens bewaren wij zeven jaar, conform de wettelijke bewaarplicht van de Belastingdienst.</p>
                        <h2>Delen met derden</h2>
                        <p>Wij verstrekken uw gegevens niet aan derden, tenzij dat nodig is voor de uitvoering van de opdracht (bijvoorbeeld een hostingpartij) of wij daartoe wettelijk verplicht zijn. Met deze partijen sluiten wij een verwerkersovereenkomst.</p>
                        <h2>Uw rechten</h2>
                        <p>U heeft het recht uw gegevens in te zien, te laten corrigeren of te laten verwijderen. Ook kunt u bezwaar maken tegen de verwerking. Neem hiervoor contact met ons op via de gegevens op de contactpagina.</p>
                        <h2>Beveiliging</h2>
                        <p>Wij nemen passende technische en organisatorische maatregelen om uw gegevens te beschermen tegen verlies of onrechtmatig gebruik. Vermoedt u misbruik? Neem dan direct contact met ons op.</p>
                        HTML,
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $attributes */
    private function page(array $attributes): Page
    {
        return Page::updateOrCreate(['slug' => $attributes['slug']], $attributes + ['is_published' => true]);
    }

    /**
     * Rebuild a page's widgets so re-seeding always reproduces the design.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $blocks
     */
    private function blocks(Page $page, array $blocks): void
    {
        $page->blocks()->delete();

        foreach ($blocks as $index => $block) {
            $page->blocks()->create([
                'type' => $block['type'],
                'data' => $block['data'],
                'sort_order' => $index,
                'is_visible' => true,
            ]);
        }
    }

    /**
     * The homepage widgets, in design order.
     *
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function homeBlocks(): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'title' => 'Uw betrouwbare ICT-partner voor thuis en op kantoor',
                    'body' => 'Johan Willemsen Computers is uw betrouwbare ICT-specialist in Opheusden. Van computerreparaties en netwerkbeheer tot cloudoplossingen, websites en complete systeemondersteuning.',
                    'size' => 'full',
                    'align' => 'center',
                    'pattern' => 'left',
                    'image' => '',
                    'video' => '',
                    'show_overlay' => true,
                    'buttons' => [
                        ['label' => 'Bekijk onze diensten', 'href' => '/diensten', 'variant' => 'primary'],
                        ['label' => 'Remote Support', 'href' => '/contact', 'variant' => 'outline'],
                    ],
                ],
            ],

            [
                'type' => 'logos',
                'data' => [
                    'heading' => '',
                    'grayscale' => false,
                    // Staff upload the real client logos; the design uses placeholders.
                    'logos' => [],
                ],
            ],

            [
                'type' => 'solution_cards',
                'data' => [
                    'heading' => 'Complete ICT-oplossingen voor elke situatie',
                    'cards' => [
                        [
                            'icon' => '/images/design/icon-support.svg',
                            'title' => 'ICT Support & Beheer',
                            'text' => 'Snelle hulp bij storingen, installaties en dagelijks IT-gebruik voor bedrijven en particulieren.',
                            'cta_label' => 'Meer info',
                            'cta_href' => '/diensten/ict-support',
                        ],
                        [
                            'icon' => '/images/design/icon-cloud.svg',
                            'title' => 'Microsoft 365 & Cloud',
                            'text' => 'Complete oplossingen voor Microsoft 365, cloudservices, software, e-mail en veilig online samenwerken.',
                            'cta_label' => 'Meer info',
                            'cta_href' => '/diensten/microsoft-365',
                        ],
                        [
                            'icon' => '/images/design/icon-network.svg',
                            'title' => 'Netwerk & Telefonie',
                            'text' => 'Betrouwbare netwerken, internet en VoIP-oplossingen.',
                            'cta_label' => 'Meer info',
                            'cta_href' => '/diensten/netwerk-telefonie',
                        ],
                        [
                            'icon' => '/images/design/icon-web.svg',
                            'title' => 'Website & Hosting',
                            'text' => 'Ontwerp van professionele websites en webshops, inclusief hosting, onderhoud en technische ondersteuning.',
                            'cta_label' => 'Meer info',
                            'cta_href' => '/diensten/website-hosting',
                        ],
                    ],
                ],
            ],

            [
                'type' => 'dienst_boxes',
                'data' => [
                    'heading' => 'Slimme technologie, persoonlijke service',
                    'intro' => 'Met jarenlange ervaring leveren wij complete ICT-oplossingen die aansluiten op uw wensen. Van installatie en beheer tot onderhoud en ondersteuning: wij regelen het voor u.',
                    // Empty = all published diensten in overview order.
                    'dienst_ids' => [],
                    'count' => 5,
                ],
            ],

            [
                'type' => 'reviews',
                'data' => [
                    'heading' => 'Wat onze klanten over ons zeggen',
                    'intro' => 'Lees waarom klanten voor Johan Willemsen Computers kiezen.',
                    'show_rating' => true,
                    'rating_label' => 'Great',
                    'rating_score' => 5,
                    'rating_count' => 4,
                    'rating_url' => '',
                    // Placeholder copy from the design — replace with the real
                    // Trustpilot reviews in de admin.
                    'reviews' => array_fill(0, 4, [
                        'name' => 'A.G. de Vree-van Klei',
                        'date' => '2025-11-14',
                        'rating' => 5,
                        'text' => 'Goede en snelle service! Aanbevolen dit bedrijf.',
                    ]),
                ],
            ],

            [
                'type' => 'projects',
                'data' => [
                    'heading' => 'Oplossingen waar we trots op zijn',
                    'intro' => 'Bekijk een selectie van websites, ICT-projecten en technische oplossingen die wij hebben gerealiseerd.',
                    'layout' => 'slider',
                    'columns' => 3,
                    // Empty = all published projects in overview order.
                    'project_ids' => [],
                    'count' => 4,
                    'cta_label' => 'Bekijk alle projecten',
                    'cta_href' => '/projecten',
                ],
            ],

            [
                'type' => 'cta_banner',
                'data' => [
                    'title' => 'Direct hulp nodig?',
                    'body' => 'Heeft u een technisch probleem? Met TeamViewer kunnen wij veilig op afstand verbinding maken met uw computer en u direct helpen.',
                    'cta_label' => 'Start remote support',
                    'cta_href' => '/contact',
                    'show_pattern' => true,
                ],
            ],
        ];
    }
}
