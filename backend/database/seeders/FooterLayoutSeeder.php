<?php

namespace Database\Seeders;

use App\Models\BlockLayout;
use Illuminate\Database\Seeder;

/** The four footer columns from the design (Figma 31789:1872). */
class FooterLayoutSeeder extends Seeder
{
    public function run(): void
    {
        $layout = BlockLayout::updateOrCreate(
            ['key' => 'footer'],
            ['name' => 'Footer', 'is_active' => true],
        );

        // Rebuild so re-seeding always reproduces the designed footer.
        $layout->columns()->delete();

        foreach ($this->columns() as $index => $blocks) {
            $column = $layout->columns()->create(['sort_order' => $index, 'is_visible' => true]);

            foreach ($blocks as $blockIndex => $block) {
                $column->blocks()->create([
                    'type' => $block['type'],
                    'data' => $block['data'],
                    'sort_order' => $blockIndex,
                    'is_visible' => true,
                ]);
            }
        }
    }

    /** @return array<int, array<int, array{type: string, data: array<string, mixed>}>> */
    private function columns(): array
    {
        return [
            [[
                'type' => 'about',
                'data' => [
                    'show_logo' => true,
                    'text' => 'Uw betrouwbare ICT-specialist in Opheusden en omgeving. Hardware, software, cloud en netwerk alles onder één dak.',
                    'show_socials' => true,
                ],
            ]],
            [[
                'type' => 'link_list',
                'data' => [
                    'heading' => 'Informatie',
                    'items' => [
                        ['label' => 'Home', 'href' => '/'],
                        ['label' => 'Over ons', 'href' => '/over-ons'],
                        ['label' => 'Diensten', 'href' => '/diensten'],
                        ['label' => 'Blog', 'href' => '/blog'],
                        ['label' => 'Terms and Conditions', 'href' => '/algemene-voorwaarden'],
                        ['label' => 'Privacy Policy', 'href' => '/privacy-policy'],
                    ],
                ],
            ]],
            [[
                'type' => 'link_list',
                'data' => [
                    'heading' => 'Diensten',
                    'items' => [
                        ['label' => 'Hardware', 'href' => '/diensten/ict-support'],
                        ['label' => 'Software', 'href' => '/diensten/microsoft-365'],
                        ['label' => 'Webdesign', 'href' => '/diensten/website-hosting'],
                        ['label' => 'Cloud Diensten', 'href' => '/diensten/cloud-diensten'],
                        ['label' => 'Telefonie', 'href' => '/diensten/netwerk-telefonie'],
                    ],
                ],
            ]],
            [[
                'type' => 'contact_info',
                'data' => ['heading' => 'Contact', 'from_settings' => true],
            ]],
        ];
    }
}
