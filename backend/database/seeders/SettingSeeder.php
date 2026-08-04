<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/** Site-wide settings, seeded with the values from the design. */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Company
            'company_name' => 'Johan Willemsen Computers',
            'tagline' => 'Uw betrouwbare ICT-specialist in Opheusden en omgeving. Hardware, software, cloud en netwerk alles onder één dak.',
            'logo' => '/images/design/logo.png',
            'logo_white' => '/images/design/logo.png',
            'company_vat' => 'NL001914453B09',
            'company_coc' => '11063125',

            // Contact
            'footer_contact_heading' => 'Contact',
            'contact_email' => 'info@johanwillemsen.nl',
            'contact_phone' => '0488-712003',
            'contact_phone_hours' => '9u t/m 21:30u',
            'contact_fax' => '0847-102831',
            'contact_address_line1' => 'Hazelaarstraat 15',
            'contact_address_line2' => '4043PG OPHEUSDEN',

            // Header
            'header_cta_label' => 'Remote Support',
            'header_cta_href' => '/contact',

            // Top bar USPs
            'topbar_usp_1' => 'Geverifieerde reviews op Trustpilot',
            'topbar_usp_2' => 'Snelle service in de regio',
            'topbar_usp_3' => 'Betrouwbare ICT-partner',

            // Socials — filled in by staff; empty values hide the icon.
            'social_twitter' => '#',
            'social_facebook' => '#',
            'social_linkedin' => '',
            'social_instagram' => '',

            // Reviews
            'trustpilot_url' => '',
            'trustpilot_score' => 'Great',
            'trustpilot_count' => '4',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
