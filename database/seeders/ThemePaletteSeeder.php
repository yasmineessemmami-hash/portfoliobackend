<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThemePalette;

class ThemePaletteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default theme palette colors from theme.css (HSL format)
        $defaultPalette = [
            // Base Colors
            "--color-background" => "220 25% 6%",
            "--color-foreground" => "210 20% 98%",
            "--color-card" => "220 25% 10%",
            "--color-card-foreground" => "210 20% 98%",
            "--color-popover" => "220 25% 10%",
            "--color-popover-foreground" => "210 20% 98%",
            
            // Primary Colors
            "--color-primary" => "175 70% 45%",
            "--color-primary-foreground" => "220 25% 6%",
            
            // Secondary Colors
            "--color-secondary" => "220 20% 14%",
            "--color-secondary-foreground" => "210 20% 90%",
            
            // Muted Colors
            "--color-muted" => "220 15% 20%",
            "--color-muted-foreground" => "215 15% 55%",
            
            // Accent Colors
            "--color-accent" => "175 60% 35%",
            "--color-accent-foreground" => "210 20% 98%",
            
            // Destructive Colors
            "--color-destructive" => "0 84.2% 60.2%",
            "--color-destructive-foreground" => "210 40% 98%",
            
            // Border & Input Colors
            "--color-border" => "220 15% 18%",
            "--color-input" => "220 15% 18%",
            "--color-ring" => "175 70% 45%",
            
            // Custom Colors
            "--color-glow" => "175 70% 45%",
            "--color-surface" => "220 25% 12%",
            "--color-surface-hover" => "220 25% 16%",
            "--color-text-primary" => "210 20% 98%",
            "--color-text-secondary" => "215 15% 65%",
            "--color-text-muted" => "215 10% 45%",
            
            // Status Colors
            "--color-success" => "142 76% 36%",
            "--color-error" => "0 84.2% 60.2%",
            "--color-warning" => "38 92% 50%",
            
            // Border Radius
            "--radius" => "0.75rem",
        ];

        // Create or update the default theme palette
        ThemePalette::updateOrCreate(
            ['name' => 'Default Theme'],
            [
                'palette' => $defaultPalette,
                'is_active' => true,
            ]
        );
    }
}

