<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A fresh `migrate:fresh --seed` reproduces the designed site: the staff login,
 * site settings, the CMS pages + homepage widgets, the footer columns and the
 * Diensten/Blog content.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@johanwillemsen.nl'],
            ['name' => 'Admin', 'password' => 'password', 'is_staff' => true],
        );

        $this->call([
            SettingSeeder::class,
            DienstSeeder::class,
            ProjectSeeder::class,
            BlogSeeder::class,
            PageSeeder::class,
            FooterLayoutSeeder::class,
        ]);
    }
}
