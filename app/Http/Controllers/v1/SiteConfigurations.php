<?php

namespace App\Http\Controllers\v1;

use App\Models\Theme;
use App\Models\SocialLink;
use App\Models\SiteSetting;
use App\Models\ThemePalette;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SiteConfigurations extends Controller
{

    public function getSiteConfigurations(Request $request)
    {
        // Check if the site configurations are cached
        if (Cache::has('site_configurations')) {
            $cachedData = Cache::get('site_configurations');
            return successResponse($cachedData, 'Site Configurations fetched successfully from cache.');
        }

        $siteConfigurations = $this->getSiteConfigurationsFromDatabase();

        // If null, return success with null data (empty database scenario)
        // Frontend will handle allowing only admin access
        return successResponse($siteConfigurations, $siteConfigurations ? 'Site Configurations fetched successfully from database.' : 'Site Configurations not found. Database is empty.');
    }

    public function getThemes(Request $request)
    {
        // Check if the themes are cached
        if (Cache::has('themes')) {
            return successResponse(Cache::get('themes'), 'Themes fetched successfully from cache.');
        }

        $response = $this->getThemesFromDatabase();

        //  Cache the response for 1 year
        Cache::put('themes', $response, now()->addYear());
        return successResponse($response, 'Themes fetched successfully from database.');
    }

    public function updateSiteConfigurationsFullName(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
        ]);

        // Update existing record (id=1) or create it if it doesn't exist
        $siteConfiguration = SiteSetting::updateOrCreate(
            ['id' => 1],
            ['full_name' => $validated['full_name']]
        );

        // Refresh cache (1 year) should be Queued in background later As New Feature
        $refreshedSiteConfigurations = $this->getSiteConfigurationsFromDatabase();
        Cache::put('site_configurations', $refreshedSiteConfigurations, now()->addYear());

        return successResponse(
            ["full_name" => $siteConfiguration->full_name],
            'Name updated successfully.'
        );
    }

    public function updateSiteConfigurationContactInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "contact_email" => ['required', 'string', 'max:255', 'email'],
            "contact_phone" => ['required', 'string', 'max:255', 'regex:/^\+?\d{1,4}(?:[ -]\d{1,4})+$/'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $data = $validator->validated(); // get all validated values once

        $siteConfiguration = SiteSetting::updateOrCreate(
            ['id' => 1],
            [
                'contact_email' => strtolower($data['contact_email']),
                'contact_phone' => $data['contact_phone'],
            ]
        );

        // Refresh cache (1 year) should be Queued in background later As New Feature
        $refreshedSiteConfigurations = $this->getSiteConfigurationsFromDatabase();
        Cache::put('site_configurations', $refreshedSiteConfigurations, now()->addYear());

        return successResponse(
            [
                'contact_email' => $siteConfiguration->contact_email,
                'contact_phone' => $siteConfiguration->contact_phone,
            ],
            'Contact Information updated successfully.'
        );
    }

    public function addSiteConfigurationSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "platform" => ['required', 'string', 'max:255'],
            "url" => ['required', 'string', 'max:255'],
            "icon_key" => ['required', 'string', 'max:255'],
            "sort_order" => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }
        $data = $validator->validated();
        $socialLink = SocialLink::create($data);
        // Refresh cache (1 year) should be Queued in background later As New Feature
        $refreshedSiteConfigurations = $this->getSiteConfigurationsFromDatabase();
        Cache::put('site_configurations', $refreshedSiteConfigurations, now()->addYear());
        return successResponse($socialLink, 'Social Link added successfully.');
    }

    public function updateSiteConfigurationSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'integer', 'exists:social_links,id'],
            "platform" => ['required', 'string', 'max:255'],
            "url" => ['required', 'string', 'max:255'],
            "icon_key" => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }
        $data = $validator->validated();
        $socialLink = SocialLink::find($data['id']);
        $socialLink->update($data);
        // Refresh cache (1 year) should be Queued in background later As New Feature
        $refreshedSiteConfigurations = $this->getSiteConfigurationsFromDatabase();
        Cache::put('site_configurations', $refreshedSiteConfigurations, now()->addYear());
        return successResponse($socialLink, 'Social Link updated successfully.');
    }

    public function deleteSiteConfigurationSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'integer', 'exists:social_links,id'],
        ]);
        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }
        $data = $validator->validated();
        $socialLink = SocialLink::find($data['id']);
        $socialLink->delete();
        // Refresh cache (1 year) should be Queued in background later As New Feature
        $refreshedSiteConfigurations = $this->getSiteConfigurationsFromDatabase();
        Cache::put('site_configurations', $refreshedSiteConfigurations, now()->addYear());
        return successResponse(null, 'Social Link deleted successfully.');
    }

    public function orderSiteConfigurationSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "ids" => ['required', 'array'],
            "ids.*" => ['required', 'integer', 'exists:social_links,id'],
        ]);
        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }
        $data = $validator->validated();

        // Get all social links that match the provided IDs
        $socialLinks = SocialLink::whereIn('id', $data['ids'])->get()->keyBy('id');

        // Verify all IDs exist
        if ($socialLinks->count() !== count($data['ids'])) {
            return errorResponse('Invalid social links', 422, 'Invalid social links');
        }

        // Update sort_order based on the order of IDs in the request
        foreach ($data['ids'] as $index => $id) {
            $link = $socialLinks->firstWhere('id', $id);
            if ($link) {
                $link->sort_order = $index + 1;
                $link->save();
            }
        }

        // Return updated links ordered by new sort_order
        $updatedLinks = SocialLink::whereIn('id', $data['ids'])->orderBy('sort_order', 'asc')->get();
        // Refresh cache (1 year) should be Queued in background later As New Feature
        $refreshedSiteConfigurations = $this->getSiteConfigurationsFromDatabase();
        Cache::put('site_configurations', $refreshedSiteConfigurations, now()->addYear());
        return successResponse($updatedLinks, 'Social Links ordered successfully.');
    }

    public function updateThemePalette(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'palette' => ['required', 'array'],
            'palette.*' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $data = $validator->validated();

        // Get active palette
        $activePalette = ThemePalette::where('is_active', true)->first();

        if (!$activePalette) {
            // If no active palette, create one
            $activePalette = ThemePalette::create([
                'name' => $data['name'],
                'palette' => $data['palette'],
                'is_active' => true,
            ]);
        } else {
            // Update existing active palette
            $activePalette->update([
                'name' => $data['name'],
                'palette' => $data['palette'],
            ]);
        }

        // Refresh caches
        Cache::forget('themes');
        Cache::forget('site_configurations');
        $this->getThemesFromDatabase();
        $this->getSiteConfigurationsFromDatabase();

        return successResponse($activePalette, 'Theme palette updated successfully.');
    }

    // public function resetThemePalette(Request $request)
    // {
    //     // Get default palette values from seeder
    //     $defaultPalette = [
    //         "--color-background" => "220 25% 6%",
    //         "--color-foreground" => "210 20% 98%",
    //         "--color-card" => "220 25% 10%",
    //         "--color-card-foreground" => "210 20% 98%",
    //         "--color-popover" => "220 25% 10%",
    //         "--color-popover-foreground" => "210 20% 98%",
    //         "--color-primary" => "175 70% 45%",
    //         "--color-primary-foreground" => "220 25% 6%",
    //         "--color-secondary" => "220 20% 14%",
    //         "--color-secondary-foreground" => "210 20% 90%",
    //         "--color-muted" => "220 15% 20%",
    //         "--color-muted-foreground" => "215 15% 55%",
    //         "--color-accent" => "175 60% 35%",
    //         "--color-accent-foreground" => "210 20% 98%",
    //         "--color-destructive" => "0 84.2% 60.2%",
    //         "--color-destructive-foreground" => "210 40% 98%",
    //         "--color-border" => "220 15% 18%",
    //         "--color-input" => "220 15% 18%",
    //         "--color-ring" => "175 70% 45%",
    //         "--color-glow" => "175 70% 45%",
    //         "--color-surface" => "220 25% 12%",
    //         "--color-surface-hover" => "220 25% 16%",
    //         "--color-text-primary" => "210 20% 98%",
    //         "--color-text-secondary" => "215 15% 65%",
    //         "--color-text-muted" => "215 10% 45%",
    //         "--color-success" => "142 76% 36%",
    //         "--color-error" => "0 84.2% 60.2%",
    //         "--color-warning" => "38 92% 50%",
    //         "--radius" => "0.75rem",
    //     ];

    //     // Get active palette
    //     $activePalette = ThemePalette::where('is_active', true)->first();

    //     if (!$activePalette) {
    //         $activePalette = ThemePalette::create([
    //             'name' => 'Default Theme',
    //             'palette' => $defaultPalette,
    //             'is_active' => true,
    //         ]);
    //     } else {
    //         $activePalette->update([
    //             'palette' => $defaultPalette,
    //         ]);
    //     }

    //     // Refresh caches
    //     Cache::forget('themes');
    //     Cache::forget('site_configurations');
    //     $this->getThemesFromDatabase();
    //     $this->getSiteConfigurationsFromDatabase();

    //     return successResponse($activePalette, 'Theme palette reset to default successfully.');
    // }

    public function restoreDefaultThemePalette(Request $request)
    {
        // Set all palettes to inactive
        ThemePalette::where('is_active', true)->update(['is_active' => false]);

        // Find or create default theme palette and activate it
        $defaultPalette = ThemePalette::where('name', 'Default Theme')->first();

        if (!$defaultPalette) {
            // Create default palette if it doesn't exist
            $defaultPaletteData = [
                "--color-background" => "220 25% 6%",
                "--color-foreground" => "210 20% 98%",
                "--color-card" => "220 25% 10%",
                "--color-card-foreground" => "210 20% 98%",
                "--color-popover" => "220 25% 10%",
                "--color-popover-foreground" => "210 20% 98%",
                "--color-primary" => "175 70% 45%",
                "--color-primary-foreground" => "220 25% 6%",
                "--color-secondary" => "220 20% 14%",
                "--color-secondary-foreground" => "210 20% 90%",
                "--color-muted" => "220 15% 20%",
                "--color-muted-foreground" => "215 15% 55%",
                "--color-accent" => "175 60% 35%",
                "--color-accent-foreground" => "210 20% 98%",
                "--color-destructive" => "0 84.2% 60.2%",
                "--color-destructive-foreground" => "210 40% 98%",
                "--color-border" => "220 15% 18%",
                "--color-input" => "220 15% 18%",
                "--color-ring" => "175 70% 45%",
                "--color-glow" => "175 70% 45%",
                "--color-surface" => "220 25% 12%",
                "--color-surface-hover" => "220 25% 16%",
                "--color-text-primary" => "210 20% 98%",
                "--color-text-secondary" => "215 15% 65%",
                "--color-text-muted" => "215 10% 45%",
                "--color-success" => "142 76% 36%",
                "--color-error" => "0 84.2% 60.2%",
                "--color-warning" => "38 92% 50%",
                "--radius" => "0.75rem",
            ];
            $defaultPalette = ThemePalette::create([
                'name' => 'Default Theme',
                'palette' => $defaultPaletteData,
                'is_active' => true,
            ]);
        } else {
            $defaultPalette->update(['is_active' => true]);
        }

        // Refresh caches
        Cache::forget('themes');
        Cache::forget('site_configurations');
        $this->getThemesFromDatabase();
        $this->getSiteConfigurationsFromDatabase();

        return successResponse($defaultPalette, 'Default theme palette restored successfully.');
    }

    public function createNewThemePalette(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'palette' => ['required', 'array'],
            'palette.*' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $data = $validator->validated();

        // Set all existing palettes to inactive
        ThemePalette::where('is_active', true)->update(['is_active' => false]);

        // Create new palette
        $newPalette = ThemePalette::create([
            'name' => $data['name'],
            'palette' => $data['palette'],
            'is_active' => true,
        ]);

        // Refresh caches
        Cache::forget('themes');
        Cache::forget('site_configurations');
        $this->getThemesFromDatabase();
        $this->getSiteConfigurationsFromDatabase();

        return successResponse($newPalette, 'New theme palette created successfully.');
    }

    public function toggleEventTheme(Request $request)
    {
        // Get all valid theme keys from database
        $validThemeKeys = Theme::pluck('theme_key')->toArray();
        $inRule = !empty($validThemeKeys) ? 'in:' . implode(',', $validThemeKeys) : '';

        $validator = Validator::make($request->all(), [
            'theme_key' => ['required', 'string', $inRule],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $themeKey = $validator->validated()['theme_key'];

        // Set all themes to inactive
        Theme::where('is_active', true)->update(['is_active' => false]);

        // Find the requested theme and activate it
        $theme = Theme::where('theme_key', $themeKey)->first();

        if ($theme) {
            $theme->update(['is_active' => true]);
        }

        // Refresh caches
        Cache::forget('themes');
        Cache::forget('site_configurations');
        $this->getThemesFromDatabase();
        $this->getSiteConfigurationsFromDatabase();

        return successResponse($theme, 'Event theme toggled successfully.');
    }

    public function toggleMaintenanceMode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_mode' => ['required', 'string', 'in:normal,maintenance'],
            'maintenance_hours' => ['nullable', 'integer', 'min:0', 'max:99'],
            'maintenance_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $validated = $validator->validated();
        $siteMode = $validated['site_mode'];

        $updateData = ['site_mode' => $siteMode];

        if ($siteMode === 'maintenance') {
            $hours = $validated['maintenance_hours'] ?? 0;
            $minutes = $validated['maintenance_minutes'] ?? 0;

            if ($hours > 0 || $minutes > 0) {
                $updateData['maintenance_until'] = now()->addHours($hours)->addMinutes($minutes);
            } else {
                $updateData['maintenance_until'] = null;
            }
        } else {
            // Clear time when switching to normal
            $updateData['maintenance_until'] = null;
        }

        $siteConfiguration = SiteSetting::updateOrCreate(
            ['id' => 1],
            $updateData
        );

        // Refresh cache
        Cache::forget('site_configurations');
        $this->getSiteConfigurationsFromDatabase();

        return successResponse(['site_mode' => $siteConfiguration->site_mode], 'Maintenance mode updated successfully.');
    }

    public function activateThemePalette(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'palette_id' => ['required', 'integer', 'exists:theme_palettes,id'],
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $paletteId = $validator->validated()['palette_id'];

        // Set all palettes to inactive
        ThemePalette::where('is_active', true)->update(['is_active' => false]);

        // Activate the requested palette
        $palette = ThemePalette::find($paletteId);
        $palette->update(['is_active' => true]);

        // Refresh caches
        Cache::forget('themes');
        Cache::forget('site_configurations');
        $this->getThemesFromDatabase();
        $this->getSiteConfigurationsFromDatabase();

        return successResponse($palette, 'Theme palette activated successfully.');
    }

    // ---------------- Private Functions -------------
    // ---------------- Private Functions -------------
    // ---------------- Private Functions -------------
    // ---------------- Private Functions -------------
    // ---------------- Private Functions -------------

    public function getSiteConfigurationsFromDatabase()
    {
        $siteConfigurations = SiteSetting::find(1);

        // Return null if site configurations don't exist (empty database)
        // Frontend will handle allowing only admin access in this case
        if (!$siteConfigurations) {
            return null;
        }

        $socialLinks = SocialLink::orderBy('sort_order', 'asc')->get();
        $theme = Theme::where('is_active', true)->first();
        $themePalette = ThemePalette::where('is_active', true)->first();

        // Calculate remaining hours and minutes from maintenance_until
        $maintenanceHours = null;
        $maintenanceMinutes = null;
        if ($siteConfigurations->site_mode === 'maintenance' && $siteConfigurations->maintenance_until) {
            $diff = now()->diff($siteConfigurations->maintenance_until);
            if ($siteConfigurations->maintenance_until->isFuture()) {
                $maintenanceHours = $diff->h + ($diff->days * 24);
                $maintenanceMinutes = $diff->i;
            } else {
                $maintenanceHours = 0;
                $maintenanceMinutes = 0;
            }
        }

        //  Format Response
        $response = [
            'site_configurations' => [
                'site_mode' => $siteConfigurations->site_mode ?? 'normal', // normal, maintenance
                'maintenance_hours' => $maintenanceHours, // Calculated remaining hours
                'maintenance_minutes' => $maintenanceMinutes, // Calculated remaining minutes
                'maintenance_until' => $siteConfigurations->maintenance_until ? $siteConfigurations->maintenance_until->toIso8601String() : null,
                'version' => $siteConfigurations->version ?? null,
                'themes' => [
                    'theme' => $theme ? [
                        'id' => $theme->id,
                        'name' => $theme->name,
                        'key' => $theme->theme_key,
                        'is_active' => $theme->is_active,
                    ] : null, // events Mode Like Christmas, Halloween, etc.
                    'theme_palette' => $themePalette ? [
                        'id' => $themePalette->id,
                        'name' => $themePalette->name,
                        'palette' => $themePalette->palette,
                        'is_active' => $themePalette->is_active,
                    ] : null, // theme palette for Style and color Plate (if null then use default theme palette)
                ],
            ],
            'personal_information' => [
                'full_name' => $siteConfigurations->full_name ?? null,
                'contact_email' => $siteConfigurations->contact_email ?? null,
                'contact_phone' => $siteConfigurations->contact_phone ?? null,
                'social_links' => $socialLinks->map(function ($link) {
                    return [
                        'id' => $link->id,
                        'platform' => $link->platform,
                        'url' => $link->url,
                        'icon_key' => $link->icon_key,
                        'sort_order' => $link->sort_order,
                    ];
                }),
            ],
        ];

        //  Cache the response for 1 year
        Cache::put('site_configurations', $response, now()->addYear());
        return $response;
    }

    public function getThemesFromDatabase()
    {
        $themes = Theme::all()->map(function ($theme) {
            return [
                'id' => $theme->id,
                'name' => $theme->name,
                'key' => $theme->theme_key,
                'is_active' => $theme->is_active,
                'created_at' => $theme->created_at,
                'updated_at' => $theme->updated_at,
            ];
        });

        $themePalettes = ThemePalette::all()->map(function ($palette) {
            return [
                'id' => $palette->id,
                'name' => $palette->name,
                'palette' => $palette->palette,
                'is_active' => $palette->is_active,
                'created_at' => $palette->created_at,
                'updated_at' => $palette->updated_at,
            ];
        });

        $response = [
            'themes' => $themes,
            'theme_palettes' => $themePalettes,
        ];
        return $response;
    }
}
