<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * The projects shown on the homepage slider and the Website & Hosting grid
 * (Figma 31793:2030 / 31848:2205).
 *
 * The design only ships four real screenshots — the remaining cards reuse them
 * as placeholders, so staff replace them with the real thing per project.
 */
class ProjectSeeder extends Seeder
{
    private const SHOTS = [
        '/images/design/project-ov-opheusden.png',
        '/images/design/project-vanmanenkleinfruit.png',
        '/images/design/project-vandoorn.png',
        '/images/design/project-willemsen-bouw.png',
    ];

    public function run(): void
    {
        // Every project gets detail-page content so each /projecten/{slug} is a
        // real page, not an empty template.
        $categories = [['label' => 'CMS Systeem'], ['label' => 'Websites']];
        $skills = [
            ['label' => 'Design'], ['label' => 'HTML5'], ['label' => 'Ontwerp'],
            ['label' => 'PHP'], ['label' => 'Responsive'],
        ];

        $projects = [
            [
                'title' => 'OV-opheusden', 'slug' => 'ov-opheusden',
                'url' => 'https://www.ov-opheusden.nl/',
                'body' => '<p>Voor Ondernemers Vereniging Opheusden ontwikkelden wij een informatieve website waarop leden, evenementen en de jaarlijkse winkelweek samenkomen. De site is eenvoudig zelf bij te werken via het CMS.</p>',
            ],
            [
                'title' => 'Vanmanenkleinfruit', 'slug' => 'vanmanenkleinfruit',
                'accent_color' => '#fcb040', 'is_featured' => true,
                'url' => 'https://www.vanmanenkleinfruit.nl/',
                'body' => '<p>Van de firma L.H. van Manen Kleinfruit te Echteld ontvingen we opdracht voor de ontwikkeling van een website met name gericht op het werven van seizoensarbeiders.</p><p>De firma is gespecialiseerd in frambozen en bessen. Het is een responsive website geworden waar men zich kan opgeven als plukker.</p>',
            ],
            [
                'title' => 'Vandoorn', 'slug' => 'vandoorn',
                'url' => 'https://www.vandoorn.net/',
                'body' => '<p>Voor Van Doorn BV bouwden wij een website die hun werk in staalconstructies laat zien, met ruimte voor vacatures en projecten.</p>',
            ],
            [
                'title' => 'Willemsen-bouw', 'slug' => 'willemsen-bouw',
                'body' => '<p>Een frisse, responsive website voor Willemsen Bouw waarin projecten en specialisaties centraal staan.</p>',
            ],
            [
                'title' => 'Van Voorthuizen Boom- & Groenverzorging B.V.', 'slug' => 'van-voorthuizen',
                'body' => '<p>Website voor een specialist in boom- en groenverzorging, met een duidelijk overzicht van diensten en werkgebied.</p>',
            ],
            [
                'title' => 'Van Rikkenmetaal', 'slug' => 'van-rikkenmetaal',
                'body' => '<p>Een zakelijke website voor Van Rikken Metaal waarin het machinepark en de mogelijkheden worden gepresenteerd.</p>',
            ],
            [
                'title' => 'Voor MVL Metaal', 'slug' => 'mvl-metaal',
                'body' => '<p>Voor MVL Metaal verzorgden wij een compacte website met focus op vindbaarheid en contact.</p>',
            ],
            [
                'title' => 'Van Voorthuizen Groenverzorging', 'slug' => 'van-voorthuizen-groen',
                'body' => '<p>Een tweede site binnen de Van Voorthuizen-familie, gericht op particuliere tuinonderhoud.</p>',
            ],
            [
                'title' => 'De Gebroeders van Eldik', 'slug' => 'gebroeders-van-eldik',
                'body' => '<p>Website voor De Gebroeders van Eldik met een heldere presentatie van hun werkzaamheden.</p>',
            ],
        ];

        foreach ($projects as $i => $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project + [
                    'image' => self::SHOTS[$i % count(self::SHOTS)],
                    'accent_color' => $project['accent_color'] ?? '#1e62d4',
                    'is_featured' => $project['is_featured'] ?? false,
                    'categories' => $categories,
                    'skills' => $skills,
                    'is_published' => true,
                    'sort_order' => $i,
                ],
            );
        }
    }
}
