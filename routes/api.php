<?php

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\v1\FAQController;
use App\Http\Controllers\v1\BlogController;
use App\Http\Controllers\v1\HomeController;
use App\Http\Controllers\v1\AboutController;
use App\Http\Controllers\v1\CacheController;
use App\Http\Controllers\v1\SkillsController;
use App\Http\Controllers\v1\ContactController;
use App\Http\Controllers\v1\ProjectsController;
use App\Http\Controllers\v1\ServicesController;
use App\Http\Controllers\v1\SiteConfigurations;
use App\Http\Controllers\v1\AdminAuthController;
use App\Http\Controllers\v1\SessionController;
use App\Http\Controllers\v1\UserKeyController;
use App\Http\Controllers\v1\AppKeyController;
use App\Http\Controllers\v1\ReplayController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::prefix('v1')->group(function () {




    // ------------------ Public Endpoints ----------------
    // ------------------ Public Endpoints ----------------
    // ------------------ Public Endpoints ----------------
    // ------------------ Public Endpoints ----------------
    // ------------------ Public Endpoints ----------------

    Route::post('site/configurations', [SiteConfigurations::class, 'getSiteConfigurations']);
    Route::post('ui/social-icons', [MainController::class, 'getSocialIcons']);
    Route::post('ui/icons', [MainController::class, 'getIcons']);
    Route::post('site/configurations/get/themes', [SiteConfigurations::class, 'getThemes']);

    //  Guest Endpoints
    //  Guest Endpoints
    //  Guest Endpoints
    Route::post('home/meta', [HomeController::class, 'getHomePageData']);
    Route::post('about', [AboutController::class, 'getAboutData']);
    Route::post('services', [ServicesController::class, 'getServicesData']);
    Route::post('projects', [ProjectsController::class, 'getProjectsData']);
    Route::post('skills', [SkillsController::class, 'getSkillsData']);
    Route::get('skills/cv/download', [SkillsController::class, 'downloadCV']);
    Route::post('blog', [BlogController::class, 'getBlogData']);
    Route::post('blog/article/{slug}', [BlogController::class, 'getArticle']);
    Route::post('blog/subscribe', [BlogController::class, 'subscribe']);
    Route::post('blog/unsubscribe', [BlogController::class, 'unsubscribe']);
    Route::get('blog/unsubscribe', [BlogController::class, 'unsubscribe']);
    Route::post('faq', [FAQController::class, 'getFAQData']);
    Route::post('contact', [ContactController::class, 'getContactData']);
    Route::post('contact/submit', [ContactController::class, 'submitContactForm']);
    //  Guest Endpoints
    //  Guest Endpoints
    //  Guest Endpoints

    // ------------------ Key Management Endpoints ----------------
    Route::post('user', [UserKeyController::class, 'store']);
    Route::post('app', [AppKeyController::class, 'store']);

    // ------------------ Replay Endpoints ----------------
    Route::prefix('replay')->group(function () {
        Route::post('start', [ReplayController::class, 'start']);
        Route::post('append', [ReplayController::class, 'append']);
    });

    Route::post('meta/pages/{page}', [MainController::class, 'getMetaPage']);


    // ------------------ Admin Auth Endpoints ----------------
    Route::prefix('admin/auth')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('me', [AdminAuthController::class, 'me'])->middleware('admin.jwt');
        Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('admin.jwt');
    });

    // ------------------ Admin Endpoints ----------------
    // ------------------ Admin Endpoints ----------------
    // ------------------ Admin Endpoints ----------------
    // ------------------ Admin Endpoints ----------------
    // ------------------ Admin Endpoints ----------------

    // All admin endpoints are protected with JWT auth middleware
    Route::middleware('admin.jwt')->group(function () {
    Route::prefix('site/configurations')->group(function () {
        Route::Post('update/full-name', [SiteConfigurations::class, 'updateSiteConfigurationsFullName']);

        Route::Post('update/contact-information', [SiteConfigurations::class, 'updateSiteConfigurationContactInformation']);

        Route::Post('add/social-links', [SiteConfigurations::class, 'addSiteConfigurationSocialLinks']);
        Route::Post('update/social-links', [SiteConfigurations::class, 'updateSiteConfigurationSocialLinks']);
        Route::delete('delete/social-links', [SiteConfigurations::class, 'deleteSiteConfigurationSocialLinks']);
        Route::post('order/social-links', [SiteConfigurations::class, 'orderSiteConfigurationSocialLinks']);


        // Theme endpoints
        Route::prefix('theme-palette')->group(function () {
            Route::post('update', [SiteConfigurations::class, 'updateThemePalette']);
            Route::post('restore-default', [SiteConfigurations::class, 'restoreDefaultThemePalette']);
            Route::post('create', [SiteConfigurations::class, 'createNewThemePalette']);
            Route::post('activate', [SiteConfigurations::class, 'activateThemePalette']);
        });

        Route::post('event-theme/toggle', [SiteConfigurations::class, 'toggleEventTheme']);
        Route::post('maintenance-mode/toggle', [SiteConfigurations::class, 'toggleMaintenanceMode']);
    });



    // Home endpoints
    Route::prefix('home')->group(function () {
        Route::post('hero/update', [HomeController::class, 'updateHomeHero']);
        Route::post('featured-projects/update', [HomeController::class, 'updateHomeFeaturedProjects']);
        Route::post('featured-projects/delete', [HomeController::class, 'deleteHomeFeaturedProject']);
    });


    // About endpoints
    Route::prefix('about')->group(function () {
        Route::post('hero/update', [AboutController::class, 'updateAboutHero']);
        Route::post('stats/update', [AboutController::class, 'updateAboutStats']);
        Route::post('introduction/update', [AboutController::class, 'updateAboutIntroduction']);
        Route::post('services/update', [AboutController::class, 'updateAboutServices']);
        Route::post('work-process/update', [AboutController::class, 'updateAboutWorkProcess']);
        Route::post('values/update', [AboutController::class, 'updateAboutValues']);
    });


    // Services endpoints
    Route::prefix('services')->group(function () {
        Route::post('hero/update', [ServicesController::class, 'updateServicesHero']);
        Route::post('items/update', [ServicesController::class, 'updateServicesItems']);
        Route::post('why-choose-me/update', [ServicesController::class, 'updateWhyChooseMe']);
        Route::post('deliverables/update', [ServicesController::class, 'updateDeliverables']);
    });

    // Projects endpoints
    Route::prefix('projects')->group(function () {
        Route::post('hero/update', [ProjectsController::class, 'updateProjectsHero']);
        Route::post('items/update', [ProjectsController::class, 'updateProjectItem']);
        Route::post('items/delete', [ProjectsController::class, 'deleteProjectItem']);
    });


    // Skills endpoints
    Route::prefix('skills')->group(function () {
        Route::post('hero/update', [SkillsController::class, 'updateSkillsHero']);
        Route::post('social-links/update', [SkillsController::class, 'updateSkillsSocialLinks']);
        Route::post('categories/update', [SkillsController::class, 'updateSkillCategories']);
        Route::post('learning-focus/update', [SkillsController::class, 'updateLearningFocus']);
    });

    // Blog endpoints
    Route::prefix('blog')->group(function () {
        Route::post('hero/update', [BlogController::class, 'updateBlogHero']);
        Route::post('author/update', [BlogController::class, 'updateBlogAuthor']);
        Route::post('posts/add', [BlogController::class, 'addBlogPost']);
        Route::post('posts/update', [BlogController::class, 'updateBlogPost']);
        Route::post('posts/delete', [BlogController::class, 'deleteBlogPost']);
    });


    // FAQ endpoints
    Route::prefix('faq')->group(function () {
        Route::post('hero/update', [FAQController::class, 'updateFAQHero']);
        Route::post('items/update', [FAQController::class, 'updateFAQItems']);
    });

    // Contact endpoints
    Route::prefix('contact')->group(function () {
        Route::post('hero/update', [ContactController::class, 'updateContactHero']);
        Route::post('infos/update', [ContactController::class, 'updateContactInfos']);
        Route::post('social-links/update', [ContactController::class, 'updateContactSocialLinks']);
        Route::post('submissions', [ContactController::class, 'getContactSubmissions']);
        Route::post('submissions/mark-read', [ContactController::class, 'markSubmissionAsRead']);
    });

    //  Cache Management Endpoints
    Route::prefix('cache')->group(function () {
        Route::post('clear', [CacheController::class, 'clearCache']);
        Route::post('refresh', [CacheController::class, 'refreshCache']);
    });

    //  All Meta Data 
    Route::post('meta/pages/{page}/update', [MainController::class, 'updateMetaPage']);

        // Replay Admin Endpoints
        Route::prefix('replay')->group(function () {
            Route::get('sessions', [ReplayController::class, 'listSessions']);
            Route::get('session/{sessionId}', [ReplayController::class, 'getSession']);
            Route::delete('session/{sessionId}', [ReplayController::class, 'deleteSession']);
        });

        // Keys Management Admin Endpoints
        Route::prefix('keys')->group(function () {
            Route::get('users', [UserKeyController::class, 'index']);
            Route::put('users/{id}', [UserKeyController::class, 'update']);
            Route::delete('users/{id}', [UserKeyController::class, 'destroy']);
            Route::get('apps', [AppKeyController::class, 'index']);
            Route::put('apps/{id}', [AppKeyController::class, 'update']);
            Route::delete('apps/{id}', [AppKeyController::class, 'destroy']);
        });
    });



    Route::get('test', function () {
        // Update password for admin joe.nassar.tech@gmail.com to password123
        $admin = Admin::where('email', 'joe.nassar.tech@gmail.com')->first();
        $admin->password = Hash::make('password123');
        $admin->save();
        return response()->json(['message' => 'Password updated successfully']);
    });
});
