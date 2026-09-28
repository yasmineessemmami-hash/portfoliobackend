<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use App\Models\MetaPage;
use Illuminate\Http\Request;
use App\Models\SocialMediaIcon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function getSocialIcons(Request $request)
    {
        // Check if the social icons are cached
        if (Cache::has('social_icons')) {
            return successResponse(Cache::get('social_icons'), 'Social Icons Fetched Successfully From Cache');
        }
        $socialIcons = SocialMediaIcon::all();
        // Adjust the Data Format 
        $socialIcons = $socialIcons->map(function ($icon) {
            return [
                'key' => $icon->key,
                'icon' => $icon->icon,
                'name' => $icon->name,
                'library' => $icon->library,
            ];
        });
        // Store in cache for 1 Year
        Cache::put('social_icons', $socialIcons,  now()->addYear());
        return successResponse($socialIcons, 'Social Icons Fetched Successfully From Database');
    }

    public function getIcons(Request $request)
    {
        if (Cache::has('icons')) {
            return successResponse(Cache::get('icons'), 'Icons Fetched Successfully From Cache');
        }
        $icons = Icon::all();
        $icons = $icons->map(function ($icon) {
            return [
                'key' => $icon->key,
                'icon' => $icon->icon,
                'name' => $icon->name,
                'library' => $icon->library,
            ];
        });
        // Store in cache for 1 Year
        Cache::put('icons', $icons, now()->addYear());
        return successResponse($icons, 'Icons Fetched Successfully From Database');
    }
    public function getMetaPage(Request $request, $page)
    {
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|in:en,ar|nullable',
        ]);
        if ($validator->fails()) {
            $locale = 'en';
        } else {
            $locale = $validator->validated()['locale'];
        }

        if (Cache::has('meta_page_' . $page . '_' . $locale)) {
            return successResponse(Cache::get('meta_page_' . $page . '_' . $locale), 'Meta page fetched successfully from cache.');
        }

        $metaPage = $this->getMetaPageFromDatabase($page, $locale);
        return successResponse($metaPage, 'Meta page fetched successfully.');
    }
    public function updateMetaPage(Request $request, $page)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'locale' => 'required|string|in:en,ar',
            'keywords' => 'required|array',
            'keywords.*' => 'string',
        ]);
        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }
        $data = $validator->validated();

        // updateOrCreate needs both page and locale in the where clause
        $metaPage = MetaPage::updateOrCreate(
            [
                'page' => $page,
                'locale' => $data['locale'],
            ],
            $data
        );

        //  Refresh cache (1 year) should be Queued in background later As New Feature
        Cache::forget('meta_page_' . $page . '_' . $data['locale']);
        $metaPage = $this->getMetaPageFromDatabase($page, $data['locale']);
        return successResponse($metaPage, 'Meta page updated successfully.');
    }

    private function getMetaPageFromDatabase($page, $locale = 'en')
    {
        $metaPage = MetaPage::where('page', $page)->where('locale', $locale)->first();
        if (!$metaPage) {
            return errorResponse('Meta page not found', 404, 'Meta page not found');
        }
        // Put in cache (1 year) should be Queued in background later As New Feature
        Cache::put('meta_page_' . $page . '_' . $locale, $metaPage, now()->addYear());
        return $metaPage;
    }
}
