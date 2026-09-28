<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Normal Theme',
                'theme_key' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Christmas Theme',
                'theme_key' => 'christmas',
                'is_active' => false,
            ],
            [
                'name' => 'Easter Theme',
                'theme_key' => 'easter',
                'is_active' => false,
            ],
            [
                'name' => 'Halloween Theme',
                'theme_key' => 'halloween',
                'is_active' => false,
            ],
        ];

        foreach ($themes as $theme) {
            Theme::updateOrCreate(
                ['theme_key' => $theme['theme_key']],
                [
                    'name' => $theme['name'],
                    'is_active' => $theme['is_active'],
                ]
            );
        }
    }
}
