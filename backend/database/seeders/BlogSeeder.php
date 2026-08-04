<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/**
 * Articles for the blog overview (Figma 31854:1496): one featured post plus a
 * 3×3 grid. Twelve are seeded so pagination has a second page to show.
 *
 * Reading time is derived from the body (BlogPost::readMinutes), so it is never
 * out of step with the text.
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->posts() as $i => $post) {
            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post + [
                    'author' => 'Johan Willemsen',
                    'is_published' => true,
                    // Newest first: the first entry becomes the featured post.
                    'published_at' => now()->subDays($i * 9),
                ],
            );
        }
    }

    /** @return array<int, array<string, string>> */
    private function posts(): array
    {
        return [
            [
                'slug' => 'waarom-cloudopslag-essentieel-is-voor-het-mkb',
                'title' => 'Waarom cloudopslag essentieel is voor het mkb in 2026',
                'category' => 'Cloud',
                'excerpt' => '<p>Steeds meer mkb-bedrijven maken de overstap naar de cloud. Wij leggen uit wat de voordelen zijn en hoe u veilig kunt migreren.</p>',
                'body' => '<p>De cloud is allang niet meer voorbehouden aan grote organisaties. Juist kleinere bedrijven profiteren van lagere kosten, betere beveiliging en de vrijheid om overal te werken.</p><h2>Waarom overstappen?</h2><p>Een lokale server vraagt onderhoud, ruimte en vervanging na een jaar of vijf. In de cloud betaalt u voor wat u gebruikt en zijn updates en back-ups inbegrepen.</p><ul><li>Altijd de laatste versie, zonder onderhoudsavonden.</li><li>Bestanden beschikbaar op kantoor, thuis en onderweg.</li><li>Automatische back-ups op een tweede locatie.</li></ul><h2>Veilig migreren</h2><p>Een migratie doet u gefaseerd. Wij brengen eerst in kaart welke bestanden en applicaties er zijn, verhuizen daarna in blokken en houden de oude omgeving nog even beschikbaar.</p><h3>Waar let u op?</h3><ul><li>Zorg dat uw data binnen de EU wordt opgeslagen.</li><li>Zet meervoudige verificatie aan voor alle gebruikers.</li><li>Test minstens één keer of een herstel ook echt werkt.</li></ul><p>Twijfelt u of de cloud bij uw organisatie past? Wij denken graag vrijblijvend mee.</p>',
            ],
            [
                'slug' => 'microsoft-365-vs-google-workspace',
                'title' => 'Microsoft 365 vs. Google Workspace: wat past bij u?',
                'category' => 'Software',
                'excerpt' => '<p>Twee grote kantoorpakketten, elk met hun eigen sterke punten. Wij vergelijken ze eerlijk zodat u de juiste keuze maakt.</p>',
                'body' => '<p>Beide pakketten bieden e-mail, opslag en samenwerking. De vraag is vooral welke het beste aansluit op hoe uw team al werkt.</p><h2>Microsoft 365</h2><p>Sterk als u met Office-documenten werkt en klanten Word- of Excel-bestanden verwachten. Outlook blijft voor veel bedrijven de standaard.</p><h2>Google Workspace</h2><p>Prettig licht en snel in de browser, met samenwerking die net wat soepeler aanvoelt. Minder geschikt als u zware Excel-modellen gebruikt.</p><h2>Ons advies</h2><p>Werkt u veel met Office-bestanden of heeft u branchesoftware die daarop aansluit? Kies Microsoft 365. Bent u volledig webgebaseerd? Dan is Workspace een prima keuze.</p>',
            ],
            [
                'slug' => 'bespaar-op-telefoonkosten-met-voip',
                'title' => 'Bespaar tot 40% op uw telefoonkosten met VoIP',
                'category' => 'VOIP',
                'excerpt' => '<p>Vaste lijnen worden uitgefaseerd. VoIP is goedkoper, flexibeler en werkt op elk apparaat — mits het goed is ingericht.</p>',
                'body' => '<p>VoIP belt via internet in plaats van via een traditionele telefoonlijn. Dat scheelt abonnementskosten en geeft veel meer mogelijkheden.</p><h2>Wat levert het op?</h2><ul><li>Eén nummer dat overgaat op kantoor én op de mobiel.</li><li>Keuzemenu\'s en openingstijden die u zelf instelt.</li><li>Geen aparte centrale meer nodig.</li></ul><h2>Waar let u op?</h2><p>De kwaliteit staat of valt met uw internetverbinding. Wij meten vooraf of uw netwerk geschikt is en richten waar nodig voorrang voor spraakverkeer in.</p>',
            ],
            [
                'slug' => 'vijf-stappen-bedrijfsnetwerk-beveiligen',
                'title' => '5 stappen om uw bedrijfsnetwerk beter te beveiligen',
                'category' => 'Beveiliging',
                'excerpt' => '<p>De meeste incidenten beginnen klein. Met deze vijf maatregelen sluit u de grootste risico\'s af.</p>',
                'body' => '<p>Beveiliging hoeft niet ingewikkeld te zijn. De meeste schade ontstaat door zaken die eenvoudig te voorkomen zijn.</p><h2>1. Zet meervoudige verificatie aan</h2><p>Een gestolen wachtwoord is dan niet genoeg om binnen te komen.</p><h2>2. Houd alles bijgewerkt</h2><p>Updates dichten precies de gaten waar aanvallers op mikken.</p><h2>3. Maak back-ups die u test</h2><p>Een back-up die nooit is teruggezet, is een aanname — geen zekerheid.</p><h2>4. Beperk rechten</h2><p>Geef medewerkers alleen toegang tot wat ze nodig hebben.</p><h2>5. Train uw mensen</h2><p>Phishing blijft de meest gebruikte ingang. Eén keer per jaar uitleg doet al veel.</p>',
            ],
            [
                'slug' => 'beste-zakelijke-laptops',
                'title' => 'De beste zakelijke laptops van dit moment',
                'category' => 'Hardware',
                'excerpt' => '<p>Een goede werklaptop gaat vijf jaar mee. Waar u op moet letten en welke modellen wij aanraden.</p>',
                'body' => '<p>Bij zakelijk gebruik telt betrouwbaarheid zwaarder dan specificaties. Een snelle machine die na twee jaar stuk is, kost meer dan een degelijke die vijf jaar meegaat.</p><h2>Waar let u op?</h2><ul><li>Minimaal 16 GB werkgeheugen — dat scheelt jarenlang irritatie.</li><li>Een SSD, geen harde schijf.</li><li>Een goede garantie met onsite service.</li></ul><p>Wij leveren en installeren de machines volledig ingericht, zodat uw medewerker direct verder kan.</p>',
            ],
            [
                'slug' => 'windows-trager-geworden',
                'title' => 'Windows trager geworden? Dit kunt u zelf doen',
                'category' => 'ICT-tips',
                'excerpt' => '<p>Een computer die traag opstart is zelden stuk. Met deze vijf controles wint u vaak al flink wat snelheid terug.</p>',
                'body' => '<p>Een trage pc is meestal geen hardwareprobleem. In de meeste gevallen draaien er te veel programma\'s mee bij het opstarten, is de schijf vol, of is er achterstallig onderhoud.</p><h2>Waar begint u?</h2><ul><li>Controleer welke programma\'s automatisch opstarten.</li><li>Zorg dat er minimaal 15% vrije schijfruimte is.</li><li>Installeer openstaande Windows-updates.</li></ul><p>Komt u er niet uit? Wij kijken graag met u mee via remote support.</p>',
            ],
            [
                'slug' => 'waarom-een-back-up-in-de-cloud',
                'title' => 'Waarom een back-up in de cloud geen luxe is',
                'category' => 'Cloud',
                'excerpt' => '<p>Een externe schijf op kantoor beschermt niet tegen brand, diefstal of ransomware. Een cloud-back-up wel.</p>',
                'body' => '<p>De 3-2-1-regel is nog altijd de standaard: drie kopieën van uw data, op twee verschillende media, waarvan één op een andere locatie.</p><h2>Wat wij regelen</h2><ul><li>Automatische dagelijkse back-ups.</li><li>Versleutelde opslag binnen de EU.</li><li>Periodieke hersteltests, zodat u zeker weet dat het werkt.</li></ul>',
            ],
            [
                'slug' => 'microsoft-365-slim-samenwerken',
                'title' => 'Microsoft 365: slim samenwerken zonder gedoe',
                'category' => 'Microsoft 365',
                'excerpt' => '<p>E-mail, agenda en documenten op één plek — mits het goed is ingericht. Dit zijn de instellingen die het verschil maken.</p>',
                'body' => '<p>Microsoft 365 is meer dan Word en Outlook. Goed ingericht scheelt het uw medewerkers dagelijks tijd.</p><h2>Onze aanpak</h2><ul><li>Migratie van bestaande e-mail zonder downtime.</li><li>Gedeelde mailboxen en agenda\'s.</li><li>Meervoudige verificatie voor iedereen.</li></ul>',
            ],
            [
                'slug' => 'wifi-dat-overal-werkt',
                'title' => 'Wifi dat overal in het pand werkt',
                'category' => 'Netwerk',
                'excerpt' => '<p>Eén router in de meterkast is zelden genoeg. Zo zorgt u voor stabiel bereik op elke werkplek.</p>',
                'body' => '<p>Wifi-problemen zijn bijna altijd een kwestie van plaatsing en aantal accesspoints, niet van een te trage internetverbinding.</p><h2>Hoe wij dat aanpakken</h2><p>Wij meten het bereik door het hele pand en bepalen op basis daarvan waar accesspoints moeten hangen. Vaak zijn er minder nodig dan gedacht — ze staan alleen op de verkeerde plek.</p>',
            ],
            [
                'slug' => 'wat-kost-een-website',
                'title' => 'Wat kost een professionele website eigenlijk?',
                'category' => 'Webdesign',
                'excerpt' => '<p>Van eenvoudige bedrijfssite tot webshop: een eerlijk overzicht van wat u kunt verwachten.</p>',
                'body' => '<p>De prijs van een website hangt vooral af van wat er achter de schermen moet gebeuren. Een presentatiesite is iets anders dan een webshop met voorraadkoppeling.</p><h2>Waar zit het verschil?</h2><ul><li>Aantal pagina\'s en de mate van maatwerk.</li><li>Of u zelf teksten en afbeeldingen wilt beheren.</li><li>Koppelingen met andere systemen.</li></ul><p>Wij maken vooraf een vaste prijsafspraak, zodat u niet voor verrassingen komt te staan.</p>',
            ],
            [
                'slug' => 'phishing-herkennen',
                'title' => 'Phishing herkennen: waar let u op?',
                'category' => 'Beveiliging',
                'excerpt' => '<p>Nepmails worden steeds overtuigender. Deze signalen verraden ze bijna altijd.</p>',
                'body' => '<p>Phishing is nog steeds de meest gebruikte manier om binnen te komen. Gelukkig zijn er een paar signalen die vrijwel altijd opgaan.</p><h2>Let op deze punten</h2><ul><li>Een afzender die net niet klopt.</li><li>Onverwachte urgentie: "reageer binnen 24 uur".</li><li>Links die naar een ander domein wijzen dan ze suggereren.</li></ul><p>Bij twijfel: bel de afzender via een nummer dat u zelf opzoekt.</p>',
            ],
            [
                'slug' => 'nieuwe-computer-uitzoeken',
                'title' => 'Een nieuwe computer uitzoeken zonder spijt',
                'category' => 'Hardware',
                'excerpt' => '<p>Meer megahertz is niet altijd beter. Zo kiest u een machine die past bij wat u er echt mee doet.</p>',
                'body' => '<p>De meeste mensen kopen te veel of net het verkeerde. Begin bij wat u dagelijks doet, niet bij de specificaties.</p><h2>Drie profielen</h2><ul><li><strong>Kantoorwerk en e-mail</strong> — een middenklasse machine met veel geheugen voldoet ruim.</li><li><strong>Beeldbewerking</strong> — investeer in geheugen en een goed scherm.</li><li><strong>Rekenwerk</strong> — hier telt de processor wél.</li></ul>',
            ],
        ];
    }
}
