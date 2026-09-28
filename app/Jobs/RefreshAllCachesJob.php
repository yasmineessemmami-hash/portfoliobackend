<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\MainController;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\v1\FAQController;
use App\Http\Controllers\v1\BlogController;
use App\Http\Controllers\v1\HomeController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\v1\AboutController;
use App\Http\Controllers\v1\SkillsController;
use App\Http\Controllers\v1\ContactController;
use App\Http\Controllers\v1\ProjectsController;
use App\Http\Controllers\v1\ServicesController;
use App\Http\Controllers\v1\SiteConfigurations;

class RefreshAllCachesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Refresh Home page cache
            $homeController = new HomeController();
            $homeData = $homeController->getHomePageDataFromDatabase();
            Cache::put('home_page_data', $homeData, now()->addYear());

            // Refresh About page cache
            $aboutController = new AboutController();
            $aboutData = $aboutController->getAboutDataFromDatabase();
            Cache::put('about_page_data', $aboutData, now()->addYear());

            // Refresh Services page cache
            $servicesController = new ServicesController();
            $servicesData = $servicesController->getServicesDataFromDatabase();
            Cache::put('services_page_data', $servicesData, now()->addYear());

            // Refresh Projects page cache
            $projectsController = new ProjectsController();
            $projectsData = $projectsController->getProjectsDataFromDatabase();
            Cache::put('projects_page_data', $projectsData, now()->addYear());

            // Refresh Skills page cache
            $skillsController = new SkillsController();
            $skillsData = $skillsController->getSkillsDataFromDatabase();
            Cache::put('skills_page_data', $skillsData, now()->addYear());

            // Refresh Blog page cache
            $blogController = new BlogController();
            $blogData = $blogController->getBlogDataFromDatabase();
            Cache::put('blog_page_data', $blogData, now()->addYear());

            // Refresh FAQ page cache
            $faqController = new FAQController();
            $faqData = $faqController->getFAQDataFromDatabase();
            Cache::put('faq_page_data', $faqData, now()->addYear());

            // Refresh Contact page cache
            $contactController = new ContactController();
            $contactData = $contactController->getContactDataFromDatabase();
            Cache::put('contact_page_data', $contactData, now()->addYear());

            // Refresh Site Configurations cache
            $siteConfigController = new SiteConfigurations();
            $siteConfigData = $siteConfigController->getSiteConfigurationsFromDatabase();
            if ($siteConfigData) {
                Cache::put('site_configurations', $siteConfigData, now()->addYear());
            }

            // Refresh Themes cache
            $themesData = $siteConfigController->getThemesFromDatabase();
            Cache::put('themes', $themesData, now()->addYear());

            // Refresh Social Icons cache
            $mainController = new MainController();
            $socialIcons = \App\Models\SocialMediaIcon::all();
            $socialIcons = $socialIcons->map(function ($icon) {
                return [
                    'key' => $icon->key,
                    'icon' => $icon->icon,
                    'name' => $icon->name,
                    'library' => $icon->library,
                ];
            });
            Cache::put('social_icons', $socialIcons, now()->addYear());

            // Refresh Icons cache
            $icons = \App\Models\Icon::all();
            $icons = $icons->map(function ($icon) {
                return [
                    'key' => $icon->key,
                    'icon' => $icon->icon,
                    'name' => $icon->name,
                    'library' => $icon->library,
                ];
            });
            Cache::put('icons', $icons, now()->addYear());

            // Note: Meta page caches are refreshed individually when accessed
            // We could refresh all meta pages here, but that would require querying all pages/locales

        } catch (\Exception $e) {
            Log::error('Failed to refresh caches: ' . $e->getMessage());
            throw $e;
        }
    }
}
