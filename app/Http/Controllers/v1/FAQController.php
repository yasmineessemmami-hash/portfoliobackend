<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\FAQHero;
use App\Models\FAQItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    public function getFAQData()
    {
        // Check cache first
        if (Cache::has('faq_page_data')) {
            return successResponse(Cache::get('faq_page_data'), 'FAQ data fetched successfully from cache.');
        }

        // Get from database if cache is empty
        $faqData = $this->getFAQDataFromDatabase();
        
        // Cache the data for guest users - expires after 1 year
        Cache::put('faq_page_data', $faqData, now()->addYear());
        
        return successResponse($faqData, 'FAQ data fetched successfully from database.');
    }

    public function updateFAQHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $hero = FAQHero::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('faq_page_data');
        return successResponse($hero, 'FAQ hero updated successfully');
    }

    public function updateFAQData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hero' => 'required|array',
            'hero.title' => 'required|string',
            'hero.subtitle' => 'nullable|string',
            'hero.description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.question' => 'required|string',
            'items.*.answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            FAQHero::updateOrCreate(['id' => 1], $data['hero']);

            FAQItem::query()->delete();
            foreach ($data['items'] as $index => $item) {
                FAQItem::create(array_merge($item, ['sort_order' => $index]));
            }

            DB::commit();
            Cache::forget('faq_page_data');
            return successResponse(null, 'FAQ data updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update FAQ data: ' . $e->getMessage());
        }
    }

    public function updateFAQItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.question' => 'required|string',
            'items.*.answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            FAQItem::query()->delete();
            foreach ($data['items'] as $index => $item) {
                FAQItem::create(array_merge($item, ['sort_order' => $index]));
            }

            DB::commit();
            Cache::forget('faq_page_data');
            return successResponse(null, 'FAQ items updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update FAQ items: ' . $e->getMessage());
        }
    }

    public function getFAQDataFromDatabase()
    {
        return [
            'hero' => FAQHero::first(),
            'items' => FAQItem::orderBy('sort_order')->get(),
        ];
    }
}
