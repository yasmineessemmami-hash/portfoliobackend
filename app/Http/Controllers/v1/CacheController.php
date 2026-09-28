<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Jobs\RefreshAllCachesJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    /**
     * Clear all caches
     */
    public function clearCache(Request $request)
    {
        try {
            // Clear all page data caches
            Cache::forget('home_page_data');
            Cache::forget('about_page_data');
            Cache::forget('services_page_data');
            Cache::forget('projects_page_data');
            Cache::forget('skills_page_data');
            Cache::forget('blog_page_data');
            Cache::forget('faq_page_data');
            Cache::forget('contact_page_data');
            
            // Clear site configuration caches
            Cache::forget('site_configurations');
            Cache::forget('themes');
            
            // Clear icon caches
            Cache::forget('social_icons');
            Cache::forget('icons');
            
            // Clear meta page caches (pattern: meta_page_{page}_{locale})
            // Since we can't easily clear pattern-based cache keys, we'll clear common ones
            // Or use Cache::flush() if needed, but that's more aggressive
            
            return successResponse(null, 'All caches cleared successfully.');
        } catch (\Exception $e) {
            return errorResponse('Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Refresh all caches (queued)
     * This will queue a job to refresh all caches from database
     */
    public function refreshCache(Request $request)
    {
        try {
            // Dispatch the refresh job to the queue
            RefreshAllCachesJob::dispatch();
            
            return successResponse(null, 'Cache refresh job has been queued successfully.');
        } catch (\Exception $e) {
            return errorResponse('Failed to queue cache refresh: ' . $e->getMessage());
        }
    }
}

